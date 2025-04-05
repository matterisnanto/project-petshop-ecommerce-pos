<?php

namespace App\Livewire;

use Livewire\Component;
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
    public $districts = [];
    public $villages = [];

    public $selectedProvince = null;
    public $selectedCity = null;
    public $selectedDistrict = null;
    public $selectedVillage = null;

    // Form fields
    public $name = '';
    public $phone = '';
    public $email = '';
    public $post_code = '';
    public $address = '';
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
        $this->loadProvinces();

        // Load saved form data if exists
        $checkoutData = Session::get('checkout_data', []);
        foreach ($checkoutData as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
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

    public function dehydrate()
    {
        // Save form data when leaving the page
        $checkoutData = [
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'selectedProvince' => $this->selectedProvince,
            'selectedCity' => $this->selectedCity,
            'selectedDistrict' => $this->selectedDistrict,
            'selectedVillage' => $this->selectedVillage,
            'post_code' => $this->post_code,
            'address' => $this->address,
            'delivery_method' => $this->delivery_method,
        ];

        Session::put('checkout_data', $checkoutData);

        if (!request()->is('shopping-cart/checkout*')) {
            Session::forget('checkout_data');
        }
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

    public function loadProvinces()
    {
        try {
            $response = Http::get('https://matterisnanto.github.io/api-wilayah-indonesia/api/provinces.json');
            $this->provinces = $response->json();
        } catch (\Exception $e) {
            $this->provinces = [];
        }
    }

    public function loadCities()
    {
        $this->reset(['cities', 'districts', 'villages', 'selectedCity', 'selectedDistrict', 'selectedVillage']);

        if (!empty($this->selectedProvince)) {
            try {
                $response = Http::get("https://matterisnanto.github.io/api-wilayah-indonesia/api/regencies/{$this->selectedProvince}.json");
                $this->cities = $response->json();
            } catch (\Exception $e) {
                $this->cities = [];
            }
        }
    }

    public function loadDistricts()
    {
        $this->reset(['districts', 'villages', 'selectedDistrict', 'selectedVillage']);

        if (!empty($this->selectedCity)) {
            try {
                $response = Http::get("https://matterisnanto.github.io/api-wilayah-indonesia/api/districts/{$this->selectedCity}.json");
                $this->districts = $response->json();
            } catch (\Exception $e) {
                $this->districts = [];
            }
        }
    }

    public function loadVillages()
    {
        $this->reset(['villages', 'selectedVillage']);

        if (!empty($this->selectedDistrict)) {
            try {
                $response = Http::get("https://matterisnanto.github.io/api-wilayah-indonesia/api/villages/{$this->selectedDistrict}.json");
                $this->villages = $response->json();
            } catch (\Exception $e) {
                $this->villages = [];
            }
        }
    }

    public function updatedDeliveryMethod()
    {
        $this->calculateTotals();
    }

    public function proceedToPayment()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'selectedProvince' => 'required',
            'selectedCity' => 'required',
            'selectedDistrict' => 'required',
            'selectedVillage' => 'required',
            'post_code' => 'required|numeric',
            'address' => 'required|string|max:500',
            'delivery_method' => 'required',
        ]);

        // Recalculate totals to ensure accuracy before proceeding
        $this->calculateCartTotals();

        // Save the complete order data to session
        $orderData = [
            'customer' => [
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'address' => [
                    'province' => $this->selectedProvince,
                    'city' => $this->selectedCity,
                    'district' => $this->selectedDistrict,
                    'village' => $this->selectedVillage,
                    'post_code' => $this->post_code,
                    'detail' => $this->address,
                ],
            ],
            'delivery_method' => $this->delivery_method,
            'cart' => $this->cartItems,
            'totals' => [
                'subtotal' => $this->subtotal,
                'shipping' => $this->shippingCost,
                'savings' => $this->savings,
                'total' => $this->total,
            ],
        ];

        Session::put('order_data', $orderData);

        // Redirect to payment page
        return redirect()->to('/payment');
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
