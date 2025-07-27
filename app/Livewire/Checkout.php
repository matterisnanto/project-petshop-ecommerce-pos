<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Product;
use Livewire\Component;
use App\Models\PaymentMethod;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use App\Models\OlshopTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

#[Title('Checkout - Cindy Petshop')]
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
    public $address = null;
    public $addressSearchQuery = '';
    public $addressSearchResults = [];
    public $showAddressDropdown = false;
    public $isSearching = false;
    public $destinationId = null;
    public $shippingServices = [];

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
        'phone' => 'required|string|max:20|regex:/^[0-9]{10,13}$/',
        'complete_address' => 'required|string|max:500',
        'address' => 'required|json',
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
        'address.required' => 'Please select an address from the dropdown.',
        'selectedCourier.required' => 'Please select a courier.',
        'selectedService.required' => 'Please select a shipping service.',
        'selectedPaymentMethod.required' => 'Please select a payment method.',
        'paymentProof.required' => 'Payment proof is required.',
        'paymentProof.image' => 'Payment proof must be an image.',
        'paymentProof.max' => 'Payment proof must not exceed 2MB.'
    ];

    public function mount()
    {
        $this->checkPreviousUrl();
        $this->loadCartData();
        $this->loadSavedFormData();
        $this->loadPaymentMethods();

        // Initialize address if already in session
        if (Session::has('checkout_data.address')) {
            $this->address = Session::get('checkout_data.address');
            $this->parseAddressFromSession();
        }
    }

    protected function parseAddressFromSession()
    {
        try {
            $addressData = json_decode($this->address, true);
            if ($addressData) {
                $this->destinationId = $addressData['destination_id'] ?? null;
                $this->postalCode = $addressData['post_code'] ?? '';
                $this->addressSearchQuery = $this->getAddressLabel($this->address);
            }
        } catch (\Exception $e) {
            Log::error('Error parsing address from session: ' . $e->getMessage());
        }
    }

    protected function checkPreviousUrl()
    {
        $previousUrl = url()->previous();
        $currentUrl = url()->current();

        if (!$previousUrl || $previousUrl === route('shoppingcart') || !str_contains($previousUrl, '/shopping-cart/checkout')) {
            Session::forget('checkout_data');
        }
    }

    protected function loadCartData()
    {
        $this->cartItems = Session::get('cart', []);
        $cartTotals = Session::get('cart_totals', []);

        $this->subtotal = $cartTotals['subtotal'] ?? 0;
        $this->savings = $cartTotals['savings'] ?? 0;
        $this->appliedPromoCode = $cartTotals['appliedPromoCode'] ?? null;
        $this->totalWeight = max(1, $cartTotals['totalWeight'] ?? 0); // Ensure minimum weight of 1 gram
        $this->itemCount = $cartTotals['itemCount'] ?? 0;

        // Only load shipping cost if checkout data exists
        if (Session::has('checkout_data')) {
            $this->shippingCost = $cartTotals['shipping_cost'] ?? 0;
        } else {
            $this->shippingCost = 0;
        }

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

        // Load shipping services if courier and destination are set
        if ($this->selectedCourier && $this->destinationId) {
            $this->getShippingCosts();
        }
    }

    public function updatedAddressSearchQuery($value)
    {
        if (strlen($value) < 3) {
            $this->addressSearchResults = [];
            $this->showAddressDropdown = true;
            $this->isSearching = false;
            return;
        }

        $this->isSearching = true;
        $this->showAddressDropdown = true;

        try {
            $this->addressSearchResults = $this->getAddressSearchResults($value);
        } catch (\Exception $e) {
            Log::error('Address search error: ' . $e->getMessage());
            $this->addressSearchResults = [];
            session()->flash('error', 'Failed to search addresses. Please try again.');
        } finally {
            $this->isSearching = false;
        }
    }

    public function selectAddress($addressJson)
    {
        $this->address = $addressJson;
        $this->addressSearchQuery = $this->getAddressLabel($addressJson);
        $this->showAddressDropdown = false;

        try {
            $addressData = json_decode($addressJson, true);
            if (!isset($addressData['destination_id'])) {
                throw new \Exception('Invalid address data - missing destination_id');
            }
            $this->destinationId = $addressData['destination_id'] ?? null;
            $this->postalCode = $addressData['post_code'] ?? '';
            Log::debug('Selected address data', $addressData);

            $this->saveFormData();

            // If courier is already selected, get shipping costs
            if ($this->selectedCourier) {
                $this->getShippingCosts();
            }
        } catch (\Exception $e) {
            Log::error('Address selection error: ' . $e->getMessage());
            session()->flash('error', 'Failed to select address. Please try again.');
        }
    }

    protected function getAddressLabel($addressJson)
    {
        try {
            $addressData = json_decode($addressJson, true);
            return implode(', ', array_filter([
                $addressData['subdistrict_name'] ?? $addressData['subdistrict'] ?? null,
                $addressData['district_name'] ?? $addressData['district'] ?? null,
                $addressData['city_name'] ?? $addressData['city'] ?? null,
                $addressData['province_name'] ?? $addressData['province'] ?? null
            ]));
        } catch (\Exception $e) {
            Log::error('Error getting address label: ' . $e->getMessage());
            return '';
        }
    }

    protected function getAddressSearchResults($search)
    {
        $apiKey = config('services.komerce.x_api_key');
        if (empty($apiKey)) {
            throw new \Exception('Komerce API key not configured');
        }

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
        ])->get('https://api-sandbox.collaborator.komerce.id/tariff/api/v1/destination/search', [
            'keyword' => $search
        ]);

        if (!$response->successful()) {
            throw new \Exception('Address search failed with status: ' . $response->status());
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
    }

    public function onUpdatedSelectedCourier()
    {
        $this->getShippingCosts();
    }

    public function getShippingCosts()
    {
        $this->reset(['shippingServices', 'selectedService', 'shippingCost']);

        if (empty($this->destinationId)) {
            Log::error('Destination ID is empty');
            return;
        }

        if (empty($this->selectedCourier)) {
            Log::error('No courier selected');
            return;
        }

        try {
            $apiKey = config('services.rajaongkir.key');
            $response = Http::asForm()->withHeaders([
                'key' => $apiKey,
            ])->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                'origin' => config('services.rajaongkir.origin_subdistrict'),
                'destination' => $this->destinationId,
                'weight' => $this->totalWeight,
                'courier' => $this->selectedCourier,
                'price' => 'lowest'
            ]);

            if (!$response->successful()) {
                Log::error('Shipping API failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'request' => [
                        'origin' => config('services.rajaongkir.origin_subdistrict'),
                        'destination' => $this->destinationId,
                        'weight' => $this->totalWeight,
                        'courier' => $this->selectedCourier
                    ]
                ]);
                throw new \Exception('Shipping API failed with status: ' . $response->status());
            }

            $data = $response->json();

            if (isset($data['data'])) {
                $this->formatShippingServices($data['data']);

                // If there's a previously selected service in session, try to reselect it
                $checkoutData = Session::get('checkout_data', []);
                if (
                    isset($checkoutData['selectedService']) &&
                    isset($this->shippingServices[$checkoutData['selectedService']])
                ) {
                    $this->onUpdatedSelectedService($checkoutData['selectedService']);
                }
            } else {
                session()->flash('error', 'No shipping options available for this destination');
            }
        } catch (\Exception $e) {
            Log::error("Error fetching shipping costs: " . $e->getMessage());
            session()->flash('error', 'Failed to load shipping options. Please try again.');
        }
    }

    protected function formatShippingServices($services)
    {
        $formattedServices = [];

        foreach ($services as $service) {
            $key = strtolower($service['code'] . '_' . str_replace(' ', '_', $service['service']));
            $formattedServices[$key] = [
                'code' => $service['code'],
                'service' => $service['service'],
                'description' => $service['description'],
                'cost' => $service['cost'],
                'etd' => $service['etd']
            ];
        }

        $this->shippingServices = $formattedServices;
    }

    public function onUpdatedSelectedService($serviceKey)
    {
        if (!isset($this->shippingServices[$serviceKey])) {
            return;
        }

        $service = $this->shippingServices[$serviceKey];
        $this->shippingCost = $service['cost'];
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
                'courier' => $service['code'],
                'service' => $service['service'],
                'description' => $service['description'],
                'cost' => $service['cost'],  // Direct value access
                'etd' => $service['etd']    // Direct value access
            ],
            'shippingCost' => $service['cost']  // Direct value access
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

        $this->saveFormData();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

        // Special handling for certain properties
        if ($propertyName === 'selectedCourier' && $this->destinationId) {
            $this->getShippingCosts();
        } elseif ($propertyName === 'selectedPaymentMethod') {
            $this->onPaymentMethodSelected($this->selectedPaymentMethod);
        }

        $this->saveFormData();
    }

    protected function saveFormData()
    {
        $checkoutData = [
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'postalCode' => $this->postalCode,
            'complete_address' => $this->complete_address,
            'address' => $this->address,
            'selectedCourier' => $this->selectedCourier,
            'selectedService' => $this->selectedService,
            'selectedPaymentMethod' => $this->selectedPaymentMethod,
            'payment_proof_path' => $this->paymentProofPath,
        ];

        Session::put('checkout_data', $checkoutData);
    }

    public function updatedPaymentProof()
    {
        $this->isUploading = true;
        $this->validateOnly('paymentProof');

        try {
            $path = $this->paymentProof->store('payment-proofs', 'public');

            $checkoutData = Session::get('checkout_data', []);
            $checkoutData['payment_proof_path'] = $path;
            Session::put('checkout_data', $checkoutData);

            $this->paymentProofPath = $path;
            $this->isUploading = false;

            session()->flash('payment_upload_success', 'Payment proof uploaded successfully!');
        } catch (\Exception $e) {
            $this->isUploading = false;
            session()->flash('payment_upload_error', 'Failed to upload payment proof: ' . $e->getMessage());
            $this->addError('paymentProof', 'Failed to upload payment proof');
        }
    }

    public function proceedOrder()
    {

        Log::debug('Starting proceedOrder');
        $this->validate();

        try {
            $this->validate();
            Log::debug('Validation passed');

            $checkoutData = Session::get('checkout_data', []);
            $cartTotals = Session::get('cart_totals', []);
            $cartItems = Session::get('cart', []);

            Log::debug('Session data retrieved', [
                'checkout_data' => $checkoutData,
                'cart_totals' => $cartTotals,
                'cart_items_count' => count($cartItems)
            ]);

            // Prepare shipping service details
            $service = $this->shippingServices[$this->selectedService] ?? null;
            if (!$service) {
                throw new \Exception('Invalid shipping service selected');
            }

            $shippingServiceDetails = [
                'courier' => $this->selectedCourier,
                'service' => $service['service'],
                'description' => $service['description'],
                'cost' => $service['cost'],  // Direct value if not array
                'etd' => $service['etd']
            ];

            // Create transaction
            $transaction = OlshopTransaction::create([
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'sub_total_amount' => $cartTotals['subtotal'],
                'promo_code_id' => $cartTotals['appliedPromoCode'] ?? null,
                'discount_amount' => $cartTotals['savings'] ?? 0,
                'grand_total_amount' => $cartTotals['total'],
                'address' => $this->address,
                'post_code' => $this->postalCode,
                'complete_address' => $this->complete_address,
                'is_paid' => false,
                'trx_id' => 'TRX-' . date('Ymd') . '-' . strtoupper(uniqid()),
                'package_resi_number' => 'Being Processed',
                'courier' => $this->selectedCourier,
                'shipping_service' => json_encode($shippingServiceDetails),
                'weight_total' => $this->totalWeight,
                'shipping_cost' => $service['cost'],
                'estimated_delivery' => $service['etd'],
                'payment_method_id' => $this->selectedPaymentMethod,
                'proof' => $this->paymentProofPath,
            ]);

            // Create order items
            foreach ($cartItems as $item) {
                Order::create([
                    'olshop_transaction_id' => $transaction->id,
                    'type' => 'product',
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                ]);
            }

            // Clear session
            Session::forget(['cart', 'cart_totals', 'checkout_data']);

            return redirect()->route('order-confirmation', ['transaction_id' => $transaction->trx_id]);
        } catch (\Exception $e) {
            Log::error('Error processing order: ' . $e->getMessage());
            $this->addError('order_error', 'Error processing order: ' . $e->getMessage());
            session()->flash('order_error', 'Failed to process order. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.pages.checkout');
    }
}
