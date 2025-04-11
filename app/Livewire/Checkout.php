<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PaymentMethod;
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
    public $shippingServices = [];

    public $selectedProvince = null;
    public $selectedCity = null;
    public $selectedCourier = null;
    public $selectedService = null;

    public $paymentMethods = [];
    public $selectedPaymentMethod = null;
    public $accountNumber = '';

    // Form fields
    public $name = '';
    public $phone = '';
    public $email = '';
    public $postalCode = '';
    public $complete_address = '';

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
        $this->loadPaymentMethods();
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
        $this->reset(['cities', 'selectedCity', 'postalCode', 'shippingServices']);

        if (empty($this->selectedProvince)) {
            return;
        }

        try {
            $response = Http::withHeaders([
                'key' => config('services.rajaongkir.key')
            ])->get('https://api.rajaongkir.com/starter/city', [
                'province' => $this->selectedProvince
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->cities = $data['rajaongkir']['results'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Error loading cities: ' . $e->getMessage());
            $this->cities = [];
        }
    }

    public function updatedSelectedCity($cityId)
    {
        if (!$cityId) {
            $this->postalCode = '';
            return;
        }

        // Cari postal code dari data kota yang sudah dimuat
        foreach ($this->cities as $city) {
            if ($city['city_id'] == $cityId) {
                $this->postalCode = $city['postal_code'] ?? '';
                return;
            }
        }

        // Jika tidak ditemukan, ambil dari API
        $this->fetchPostalCodeFromApi($cityId);
    }

    protected function fetchPostalCodeFromApi($cityId)
    {
        try {
            $response = Http::withHeaders([
                'key' => config('services.rajaongkir.key')
            ])->get('https://api.rajaongkir.com/starter/city', [
                'id' => $cityId
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $city = $data['rajaongkir']['results'] ?? null;

                if ($city && isset($city['postal_code'])) {
                    $this->postalCode = $city['postal_code'];
                }
            }
        } catch (\Exception $e) {
            Log::error("Error fetching postal code: " . $e->getMessage());
        }
    }

    public function loadPostalCode($cityId)
    {
        $this->selectedCity = $cityId; // Update property terlebih dahulu

        if (!$cityId) {
            $this->postalCode = '';
            return;
        }

        // Cari postal code dari data kota yang sudah dimuat
        foreach ($this->cities as $city) {
            if ($city['city_id'] == $cityId) {
                $this->postalCode = $city['postal_code'] ?? '';
                return;
            }
        }

        // Jika tidak ditemukan, ambil dari API
        $this->fetchPostalCodeFromApi($cityId);
    }

    public function onUpdatedSelectedCourier()
    {
        $this->getShippingCosts();
    }

    public function getShippingCosts()
    {
        $this->shippingServices = [];
        $this->selectedService = null;
        $this->shippingCost = 0;

        if (empty($this->selectedCity) || empty($this->selectedCourier)) {
            return;
        }

        // Ensure minimum weight of 1 gram
        $weight = max(1, $this->totalWeight);

        try {
            $response = Http::withHeaders([
                'key' => config('services.rajaongkir.key')
            ])->post(config('services.rajaongkir.base_url') . '/cost', [
                'origin' => config('services.rajaongkir.origin_city'),
                'destination' => $this->selectedCity,
                'weight' => $weight,
                'courier' => $this->selectedCourier
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::debug('Shipping API Response:', $data);

                if (isset($data['rajaongkir']['results'][0]['costs'])) {
                    $this->shippingServices = $data['rajaongkir']['results'][0]['costs'];
                } else {
                    session()->flash('error', 'No shipping options available for this destination');
                }
            } else {
                session()->flash('error', 'Failed to get shipping options: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Error fetching shipping costs: " . $e->getMessage());
            session()->flash('error', 'Failed to load shipping options. Please try again.');
        }
    }

    public function updatedSelectedService($serviceKey)
    {
        if (isset($this->shippingServices[$serviceKey])) {
            $service = $this->shippingServices[$serviceKey];
            $this->shippingCost = $service['cost'][0]['value'];
            $this->calculateTotals();
        }
    }

    public function loadPaymentMethods()
    {
        $this->paymentMethods = PaymentMethod::where('olshop_transaction', true)->get();
    }

    public function onPaymentMethodSelected($value)
    {
        $this->accountNumber = '';

        if ($value) {
            $method = PaymentMethod::find($value);
            if ($method) {
                $this->accountNumber = $method->account_number;
            }
        }
    }

    protected function saveCheckoutData()
    {
        $checkoutData = [
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'selectedProvince' => $this->selectedProvince,
            'selectedCity' => $this->selectedCity,
            'postalCode' => $this->postalCode,
            'complete_address' => $this->complete_address,
            'selectedCourier' => $this->selectedCourier,
            'selectedService' => $this->selectedService
        ];

        Session::put('checkout_data', $checkoutData);
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
        $this->total = $this->subtotal + $this->shippingCost - $this->savings;
    }

    public function resetCheckoutData()
    {
        Session::forget('checkout_data');
    }

    public function render()
    {
        return view('livewire.pages.checkout');
    }
}
