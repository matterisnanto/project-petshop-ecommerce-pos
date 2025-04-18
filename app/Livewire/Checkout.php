<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Product;
use Livewire\Component;
use App\Models\PaymentMethod;
use Livewire\WithFileUploads;
use App\Models\OlshopTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class Checkout extends Component
{
    use WithFileUploads;

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
    public $paymentProof;
    public $paymentProofPath;
    public $isUploading = false;

    // Form fields
    public $name = '';
    public $phone = '';
    public $email = '';
    public $postalCode = '';
    public $complete_address = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'complete_address' => 'required|string|max:500',
        'selectedProvince' => 'required',
        'selectedCity' => 'required',
        'selectedCourier' => 'required',
        'selectedService' => 'required',
        'selectedPaymentMethod' => 'required',
        'paymentProof' => 'required|image|max:2048'
    ];

    protected $messages = [
        'name.required' => 'Full name is required.',
        'email.required' => 'Email is required.',
        'email.email' => 'Please enter a valid email address.',
        'phone.required' => 'Phone number is required.',
        'phone.regex' => 'Phone number must be 10-13 digits.',
        'complete_address.required' => 'Complete address is required.',
        'selectedProvince.required' => 'Please select a province.',
        'selectedCity.required' => 'Please select a city/regency.',
        'selectedCourier.required' => 'Please select a courier.',
        'selectedService.required' => 'Please select a shipping service.',
        'selectedPaymentMethod.required' => 'Please select a payment method.'
    ];

    public function mount()
    {
        $this->checkPreviousUrl();
        $this->loadCartData();

        // Jika tidak ada checkout_data, reset shipping cost
        if (!Session::has('checkout_data')) {
            $this->shippingCost = 0;
            $this->selectedService = null;
            $this->shippingServices = [];
            $this->updateCartTotalsWithShipping(0);
        }

        $this->loadSavedFormData();
        $this->loadDependentData();
        $this->loadProvinces();
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
        if ($this->selectedProvince && empty($this->cities)) {
            $this->loadCities();
        }

        if ($this->selectedCity && empty($this->postalCode)) {
            $this->loadPostalCode($this->selectedCity);
        }

        if ($this->selectedCourier && $this->selectedCity && empty($this->shippingServices)) {
            $this->getShippingCosts();
        }

        if ($this->selectedPaymentMethod && empty($this->accountNumber)) {
            $this->onPaymentMethodSelected($this->selectedPaymentMethod);
        }

        if (isset($checkoutData['selectedServiceDetails'])) {
            $this->shippingCost = $checkoutData['selectedServiceDetails']['cost'];
            $this->updateCartTotalsWithShipping($this->shippingCost);
        }
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
        $this->reset(['cities', 'selectedCity', 'postalCode', 'selectedCourier', 'shippingServices']);

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
                $this->saveFormData(); // Save after loading cities
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
        $this->selectedCity = $cityId;
        $this->saveFormData(); // Save after updating city

        // Rest of the method remains the same...
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

    public function onUpdatedSelectedService($serviceKey)
    {
        if (!isset($this->shippingServices[$serviceKey])) {
            return;
        }

        $service = $this->shippingServices[$serviceKey];
        $this->shippingCost = $service['cost'][0]['value'];
        $this->selectedService = $serviceKey;

        $this->saveShippingServiceToSession($serviceKey);
        $this->updateCartTotalsWithShipping($this->shippingCost);
    }

    protected function saveShippingServiceToSession($serviceKey)
    {
        $service = $this->shippingServices[$serviceKey];

        $serviceDetails = [
            'selectedService' => $serviceKey,
            'selectedServiceDetails' => [
                'courier' => $this->selectedCourier,
                'service' => $service['service'],
                'description' => $service['description'],
                'cost' => $service['cost'][0]['value'],
                'etd' => $service['cost'][0]['etd']
            ],
            'shippingCost' => $service['cost'][0]['value']
        ];

        $checkoutData = Session::get('checkout_data', []);
        $checkoutData = array_merge($checkoutData, $serviceDetails);
        Session::put('checkout_data', $checkoutData);
    }

    protected function updateCartTotalsWithShipping($shippingCost)
    {
        $cartTotals = Session::get('cart_totals', []);

        $cartTotals['shipping_cost'] = $shippingCost;
        $cartTotals['total'] = ($cartTotals['subtotal'] ?? 0) + $shippingCost - ($cartTotals['savings'] ?? 0);

        Session::put('cart_totals', $cartTotals);

        // Update component properties
        $this->shippingCost = $shippingCost;
        $this->total = $cartTotals['total'];
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

        $this->saveFormData(); // Save after updating payment method
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        $this->saveFormData();

        // Special handling for certain properties
        if ($propertyName === 'selectedProvince') {
            $this->loadCities();
        } elseif ($propertyName === 'selectedCity') {
            $this->loadPostalCode($this->selectedCity);
        } elseif ($propertyName === 'selectedCourier') {
            $this->getShippingCosts();
        } elseif ($propertyName === 'selectedPaymentMethod') {
            $this->onPaymentMethodSelected($this->selectedPaymentMethod);
        }

        // Recalculate totals when relevant fields change
        // if (in_array($propertyName, ['selectedService', 'selectedPaymentMethod'])) {
        //     $this->calculateTotals();
        // }
    }

    public function saveField($field)
    {
        $this->validateOnly($field);
        $this->persistFieldToSession($field, $this->$field);
    }
    public function updatedName($value)
    {
        $this->validateOnly('name');
        $this->persistFieldToSession('name', $value);
    }

    public function updatedPhone($value)
    {
        $this->validateOnly('phone');
        $this->persistFieldToSession('phone', $value);
    }
    public function updatedEmail($value)
    {
        $this->validateOnly('email');
        $this->persistFieldToSession('email', $value);
    }
    public function updatedCompleteAddress($value)
    {
        $this->validateOnly('complete_address');
        $this->persistFieldToSession('complete_address', $value);
    }

    protected function persistFieldToSession($field, $value)
    {
        $checkoutData = Session::get('checkout_data', []);
        $checkoutData[$field] = $value;
        Session::put('checkout_data', $checkoutData);
    }

    protected function saveFormData()
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
            'selectedPaymentMethod' => $this->selectedPaymentMethod,
            'payment_proof_path' => $this->paymentProofPath,
        ];

        // Jika ada selected service, tambahkan ke data
        if ($this->selectedService !== null && isset($this->shippingServices[$this->selectedService])) {
            $service = $this->shippingServices[$this->selectedService];
            $checkoutData['selectedService'] = $this->selectedService;
            $checkoutData['selectedServiceDetails'] = [
                'courier' => $this->selectedCourier,
                'service' => $service['service'],
                'description' => $service['description'],
                'cost' => $service['cost'][0]['value'],
                'etd' => $service['cost'][0]['etd']
            ];
            $checkoutData['shippingCost'] = $service['cost'][0]['value'];
        }

        Session::put('checkout_data', $checkoutData);

        // Debugging: Log isi session terakhir
        Log::debug('Full session data after save:', Session::get('checkout_data'));
    }



    protected function calculateCartTotals()
    {
        $this->subtotal = 0;
        $this->totalWeight = 0;

        foreach ($this->cartItems as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
            $this->totalWeight += $item['weight'] * $item['quantity'];
        }

        $cartTotals = Session::get('cart_totals', []);
        $this->savings = $cartTotals['savings'] ?? 0;
        $this->appliedPromoCode = $cartTotals['appliedPromoCode'] ?? null;
        $this->itemCount = array_sum(array_column($this->cartItems, 'quantity'));

        $cartTotals = [
            'subtotal' => $this->subtotal,
            'total' => $this->subtotal + $this->shippingCost - $this->savings,
            'savings' => $this->savings,
            'itemCount' => $this->itemCount,
            'totalWeight' => $this->totalWeight,
            'appliedPromoCode' => $this->appliedPromoCode,
            'shipping_cost' => $this->shippingCost
        ];

        Session::put('cart_totals', $cartTotals);
        $this->total = $cartTotals['total'];
    }

    public function updatedPaymentProof()
    {
        $this->isUploading = true;

        $this->validateOnly('paymentProof');

        try {
            // Store the file and get the path
            $path = $this->paymentProof->store('payment-proofs', 'public');

            // Save the path to session
            $checkoutData = Session::get('checkout_data', []);
            $checkoutData['payment_proof_path'] = $path;
            Session::put('checkout_data', $checkoutData);

            $this->paymentProofPath = $path;
            $this->isUploading = false;

            session()->flash('payment_upload_success', 'Payment proof uploaded successfully!');
        } catch (\Exception $e) {
            $this->isUploading = false;
            session()->flash('payment_upload_error', 'Failed to upload payment proof: ' . $e->getMessage());
        }
    }

    public function resetCheckoutData()
    {
        Session::forget('checkout_data');
        // Juga reset shipping-related data in cart_totals
        $cartTotals = Session::get('cart_totals', []);
        unset($cartTotals['shipping_cost']);
        Session::put('cart_totals', $cartTotals);
    }

    public function proceedOrder()
    {
        $this->validate();

        try {
            $checkoutData = Session::get('checkout_data', []);
            $cartTotals = Session::get('cart_totals', []);
            $cartItems = Session::get('cart', []);

            // Prepare shipping service details as JSON
            $shippingServiceDetails = json_encode([
                'courier' => $this->selectedCourier,
                'service' => $this->shippingServices[$this->selectedService]['service'] ?? null,
                'description' => $this->shippingServices[$this->selectedService]['description'] ?? null,
                'cost' => $this->shippingCost,
                'etd' => $this->shippingServices[$this->selectedService]['cost'][0]['etd'] ?? null
            ]);

            // Create transaction with exact field structure as in your example
            $transaction = OlshopTransaction::create([
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'sub_total_amount' => $cartTotals['subtotal'],
                'promo_code_id' => $cartTotals['appliedPromoCode'] ?? null,
                'discount_amount' => $cartTotals['savings'] ?? 0,
                'grand_total_amount' => $cartTotals['total'],
                'province' => $this->selectedProvince,
                'city_regency' => $this->selectedCity,
                'post_code' => $this->postalCode,
                'complete_address' => $this->complete_address,
                'is_paid' => false,
                'trx_id' => 'TRX-' . date('Ymd') . '-' . strtoupper(uniqid()),
                'package_resi_number' => 'Being Processed',
                'courier' => $this->selectedCourier,
                'shipping_service' => $shippingServiceDetails,
                'weight_total' => $this->totalWeight,
                'shipping_cost' => $this->shippingCost,
                'estimated_delivery' => $this->shippingServices[$this->selectedService]['cost'][0]['etd'] ?? null,
                'payment_method_id' => $this->selectedPaymentMethod,
                'proof' => $this->paymentProofPath,
            ]);

            // Create order items
            foreach ($cartItems as $items) {
                Order::create([
                    'olshop_transaction_id' => $transaction->id,
                    'type' => 'product',
                    'product_id' => $items['id'],
                    'quantity' => $items['quantity'],
                    'unit_price' => $items['price'],
                ]);
            }

            // Clear session
            Session::forget(['cart', 'cart_totals', 'checkout_data']);
            // Redirect to confirmation page
            return redirect()->route('order-confirmation', ['transaction_id' => $transaction->trx_id]);
        } catch (\Exception $e) {
            Log::error('Error processing order: ' . $e->getMessage());
            $this->addError('order_error', 'Terjadi kesalahan saat memproses order. Silakan coba lagi.');
        }
    }

    public function render()
    {
        return view('livewire.pages.checkout');
    }
}
