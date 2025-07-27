<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PaymentMethod;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

#[Title('Checkout - Cindy Petshop')]
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

    public $address = '';
    public $addressSearchQuery = '';
    public $addressSearchResults = [];
    public $showAddressDropdown = false;
    public $isSearching = false;
    public $postalCode = '';
    public $complete_address = '';
    public $isTyping = false;
    public $paymentMethods = [];

    // Form fields
    public $name = '';
    public $phone = '';
    public $email = '';
    public $selectedCourier = '';
    public $selectedService = '';
    public $selectedPaymentMethod = '';
    public $accountNumber = '';
    public $totalPaid = 0;

    public function mount()
    {
        $this->loadSavedFormData();
        $this->loadDependentData();
        $this->loadPaymentMethods();
    }

    protected function checkPreviousUrl()
    {
        $previousUrl = url()->previous();
        $currentUrl = url()->current();

        if (
            !$previousUrl || $previousUrl === route('shoppingcart') ||
            !str_contains($previousUrl, '/shopping-cart/checkout')
        ) {
            Session::forget('checkout_data');
        }
    }

    protected function loadCartData()
    {
        $this->cartItems = Session::get('cart', []);
        $cartTotals = Session::get('cart_totals', []);

        // Jangan load shipping_cost jika checkout_data tidak ada
        $checkoutData = Session::get('checkout_data', []);
        if (empty($checkoutData)) {
            unset($cartTotals['shipping_cost']);
        }

        $this->subtotal = $cartTotals['subtotal'] ?? 0;
        $this->savings = $cartTotals['savings'] ?? 0;
        $this->appliedPromoCode = $cartTotals['appliedPromoCode'] ?? null;
        $this->totalWeight = $cartTotals['totalWeight'] ?? 0;
        $this->itemCount = $cartTotals['itemCount'] ?? 0;
        $this->shippingCost = $cartTotals['shipping_cost'] ?? 0;
        $this->total = $cartTotals['total'] ?? $this->subtotal;
    }

    protected function loadSavedFormData()
    {
        $checkoutData = Session::get('checkout_data', []);

        foreach ($checkoutData as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    protected function loadDependentData()
    {

        if ($this->selectedPaymentMethod && empty($this->accountNumber)) {
            $this->onPaymentMethodSelected($this->selectedPaymentMethod);
        }
    }

    public function loadPaymentMethods()
    {
        $this->paymentMethods = PaymentMethod::where('olshop_transaction', true)->get();
    }

    public function updated($propertyName)
    {
        $this->saveFormData();

        // Special handling for certain properties
        if ($propertyName === 'selectedPaymentMethod') {
            $this->onPaymentMethodSelected($this->selectedPaymentMethod);
        }
    }

    public function updatedName($value)
    {
        $this->saveFormData();
    }

    public function updatedPhone($value)
    {
        $this->saveFormData();
    }

    public function updatedEmail($value)
    {
        $this->saveFormData();
    }

    public function updatedCompleteAddress($value)
    {
        $this->saveFormData();
    }

    public function updatedSelectedCourier($value)
    {
        $this->saveFormData();
    }

    public function updatedSelectedService($value)
    {
        $this->saveFormData();
    }

    protected function saveFormData()
    {
        $checkoutData = [
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'postalCode' => $this->postalCode,
            'complete_address' => $this->complete_address,
            'selectedCourier' => $this->selectedCourier,
            'selectedShippingService' => $this->selectedService,
            'selectedPaymentMethod' => $this->selectedPaymentMethod,
            // 'grandTotalAmount' => $this->grandTotalAmount,
            // 'paymentProofPath' => $this->paymentProofPath,

        ];

        Session::put('checkout_data', $checkoutData);
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

        $this->saveFormData(); // Save after updating payment method
    }

    public function updatedAddressSearchQuery($value)
    {
        if (strlen($value) < 3) {
            $this->addressSearchResults = [];
            $this->showAddressDropdown = true; // Tetap tampilkan dropdown untuk pesan
            $this->isSearching = false;
            return;
        }

        $this->isSearching = true;
        $this->showAddressDropdown = true;

        try {
            $this->addressSearchResults = $this->getAddressSearchResults($value);
        } finally {
            $this->isSearching = false;
        }
    }

    public function selectAddress($addressJson)
    {
        $this->address = $addressJson;
        $this->addressSearchQuery = $this->getAddressLabel($addressJson);
        $this->showAddressDropdown = false;
        $this->updatedAddress($addressJson);
    }

    protected function getAddressLabel($addressJson)
    {
        try {
            $addressData = json_decode($addressJson, true);
            return implode(', ', array_filter([
                $addressData['subdistrict'] ?? null,
                $addressData['district'] ?? null,
                $addressData['city'] ?? null,
                $addressData['province'] ?? null
            ]));
        } catch (\Exception $e) {
            return '';
        }
    }

    public function updatedAddress($value)
    {
        if ($value) {
            try {
                $addressData = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->postalCode = $addressData['post_code'] ?? '';
                    // $this->destinationId = $addressData['destination_id'] ?? null;
                    $this->saveFormData();
                }
            } catch (\Exception $e) {
                Log::error('Address update error: ' . $e->getMessage());
            }
        }
    }

    public function getAddressSearchResults($search)
    {
        $apiKey = config('services.komerce.x_api_key');
        if (empty($apiKey)) {
            Log::error('Komerce API key not configured');
            return [];
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
            ])->get('https://api-sandbox.collaborator.komerce.id/tariff/api/v1/destination/search', [
                'keyword' => $search
            ]);

            if (!$response->successful()) {
                Log::error('Address search failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return [];
            }

            $data = $response->json()['data'] ?? [];

            return collect($data)
                ->mapWithKeys(function ($item) {
                    $label = implode(', ', array_filter([
                        $item['subdistrict_name'] ?? null,
                        $item['district_name'] ?? null,
                        $item['city_name'] ?? null,
                        $item['province_name'] ?? null
                    ]));

                    $value = json_encode([
                        'subdistrict' => $item['subdistrict_name'] ?? null,
                        'district' => $item['district_name'] ?? null,
                        'city' => $item['city_name'] ?? null,
                        'province' => $item['province_name'] ?? null,
                        'post_code' => $item['zip_code'] ?? null,
                        'destination_id' => $item['id'] ?? null,
                    ]);

                    return [$value => $label];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Address search error: ' . $e->getMessage());
            return [];
        }
    }

    public function render()
    {
        return view('livewire.pages.checkout');
    }
}
