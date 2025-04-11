<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class Checkout extends Component
{
    public $cartItems = [];
    public $subtotal = 0;
    public $shippingCost = 0;
    public $total = 0;
    public $itemCount = 0;
    public $savings = 0;
    public $totalWeight = 0;
    public $appliedPromoCode = null;

    // Address fields
    public $provinces = [];
    public $cities = [];

    public $selectedProvince = null;
    public $selectedCity = null;

    // Form fields
    public $name = '';
    public $phone = '';
    public $email = '';
    public $post_code = '';
    public $complete_address = '';
    public $delivery_method = 'dhl';

    public function mount()
    {
        $previousUrl = url()->previous();
        $currentUrl = url()->current();

        if (
            !$previousUrl ||
            $previousUrl === route('shoppingcart') ||
            !str_contains($previousUrl, '/shopping-cart/checkout')
        ) {
            Session::forget('checkout_data');
        }

        $this->cartItems = Session::get('cart', []);

        // Get totals from session
        $cartTotals = Session::get('cart_totals', []);
        $this->subtotal = $cartTotals['subtotal'] ?? 0;
        $this->savings = $cartTotals['savings'] ?? 0;
        $this->appliedPromoCode = $cartTotals['appliedPromoCode'] ?? null;
        $this->totalWeight = $cartTotals['totalWeight'] ?? 0;
        $this->itemCount = $cartTotals['itemCount'] ?? 0;

        $this->calculateTotals();

        // Load saved form data if exists
        $checkoutData = Session::get('checkout_data', []);
        foreach ($checkoutData as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        $this->loadProvinces();
    }

    public function loadProvinces()
    {
        try {
            $apiKey = config('services.rajaongkir.key');

            // Validasi khusus paket starter
            if (strpos(config('services.rajaongkir.base_url'), 'starter') === false) {
                throw new \Exception('Base URL harus mengarah ke paket starter');
            }

            $response = Http::withHeaders([
                'key' => $apiKey
            ])->get('https://api.rajaongkir.com/starter/province');

            // Debugging response
            Log::debug('RajaOngkir Response:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->status() === 401) {
                throw new \Exception('API Key tidak valid untuk paket starter');
            }

            if ($response->status() === 404) {
                throw new \Exception('Pastikan menggunakan endpoint /starter/');
            }

            $data = $response->json();

            if (!isset($data['rajaongkir']['results'])) {
                throw new \Exception('Format response tidak sesuai paket starter');
            }

            $this->provinces = $data['rajaongkir']['results'];
        } catch (\Exception $e) {
            $errorMsg = 'Paket Starter Error: ' . $e->getMessage();
            Log::error($errorMsg);
            session()->flash('error', $errorMsg);
            $this->provinces = [];
        }
    }

    public function loadCities()
    {
        $this->reset(['cities', 'selectedCity']); // Reset data kota sebelumnya

        if (empty($this->selectedProvince)) {
            return;
        }

        try {
            $response = Http::withHeaders([
                'key' => config('services.rajaongkir.key')
            ])->get('https://api.rajaongkir.com/starter/city', [
                'province' => $this->selectedProvince
            ]);

            // Debugging
            Log::debug('RajaOngkir City Response:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->cities = $data['rajaongkir']['results'] ?? [];

                if (empty($this->cities)) {
                    throw new \Exception('Tidak ada kota/kabupaten untuk provinsi ini');
                }
            } else {
                throw new \Exception('Gagal memuat kota. Status: ' . $response->status());
            }
        } catch (\Exception $e) {
            $errorMsg = 'Error memuat kota: ' . $e->getMessage();
            Log::error($errorMsg);
            session()->flash('error', $errorMsg);
            $this->cities = [];
        }
    }



    protected function calculateCartTotals()
    {
        $this->subtotal = 0;
        $this->totalWeight = 0;

        // Calculate subtotal and total weight from cart items
        foreach ($this->cartItems as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
            $this->totalWeight += $item['weight'] * $item['quantity'];
        }

        // Get promo code details from session if exists
        $cartTotals = Session::get('cart_totals', []);
        $this->savings = $cartTotals['savings'] ?? 0;
        $this->appliedPromoCode = $cartTotals['appliedPromoCode'] ?? null;
        $this->itemCount = array_sum(array_column($this->cartItems, 'quantity'));

        // Calculate shipping and total
        $this->calculateTotals();

        // Update session with recalculated totals
        Session::put('cart_totals', [
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'savings' => $this->savings,
            'itemCount' => $this->itemCount,
            'totalWeight' => $this->totalWeight,
            'appliedPromoCode' => $this->appliedPromoCode
        ]);
    }


    protected function calculateTotals()
    {
        // Update shipping cost based on selected method
        $this->shippingCost = match ($this->delivery_method) {
            'dhl' => 15000,
            'fedex' => 0,
            'express' => 49000,
            default => 15000,
        };

        $this->total = $this->subtotal + $this->shippingCost - $this->savings;
    }

    public function updatedDeliveryMethod()
    {
        $this->calculateTotals();
    }


    public function resetCheckoutData()
    {
        Session::forget('checkout_data');
    }

    public function render()
    {
        return view('pages.checkout');
    }
}
