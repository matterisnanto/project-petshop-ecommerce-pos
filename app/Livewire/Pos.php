<?php

namespace App\Livewire;

use session;
use Carbon\Carbon;

use Filament\Forms;
use App\Models\Hotel;

use App\Models\Order;
use App\Models\Animals;
use App\Models\Product;
use Filament\Forms\Get;

use Filament\Forms\Set;
use Livewire\Component;
use App\Models\Breeding;
use App\Models\Grooming;
use Filament\Forms\Form;
use App\Models\PaymentMethod;
use App\Models\PetInformation;
use App\Models\PosTransaction;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;


class Pos extends Component implements HasForms
{
    use InteractsWithForms;

    public $search = '';
    public $name_customer = '';
    public $phone = '';
    public $email = '';
    public $gender = '';
    public $payment_method_id = 0;
    public $payment_methods;
    public $payment_method_account_number = '';
    public $order_items = [];
    public $total_price;
    public $paid_amount;
    public $change_amount;
    public $is_cash = 1;
    public $activeTab = 'products'; // 'products', 'animals', 'grooming', 'hotel', 'breeding'
    public $petInformation = [];

    protected $listeners = [
        'scanResult' => 'handleScanResult'
    ];

    public function render()
    {
        $products = collect();
        $animals = collect();
        $groomings = collect();
        $hotels = collect();
        $breedings = collect();

        if ($this->activeTab === 'products') {
            $products = Product::where('stock', '>', 0)
                ->when($this->search, function ($query) {
                    $query->search($this->search);
                })
                ->paginate(12);
        } elseif ($this->activeTab === 'animals') {
            $animals = Animals::where('stock', '>', 0)
                ->where('is_active', true)
                ->when($this->search, function ($query) {
                    $query->search($this->search);
                })
                ->paginate(12);
        } elseif ($this->activeTab === 'grooming') {
            $groomings = Grooming::where('is_active', true)
                ->when($this->search, function ($query) {
                    $query->search($this->search);
                })
                ->paginate(12);
        } elseif ($this->activeTab === 'hotel') {
            $hotels = Hotel::where('is_active', true)
                ->when($this->search, function ($query) {
                    $query->search($this->search);
                })
                ->paginate(12);
        } elseif ($this->activeTab === 'breeding') {
            $breedings = Breeding::where('is_active', true)
                ->when($this->search, function ($query) {
                    $query->search($this->search);
                })
                ->paginate(12);
        }

        return view('livewire.pos', [
            'products' => $products,
            'animals' => $animals,
            'groomings' => $groomings,
            'hotels' => $hotels,
            'breedings' => $breedings,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Form Checkout')
                    ->schema([
                        Forms\Components\TextInput::make('name_customer')
                            ->maxLength(255)
                            ->default(fn() => $this->name_customer),
                        Forms\Components\TextInput::make('phone')
                            ->maxLength(255)
                            ->default(fn() => $this->phone),
                        Forms\Components\TextInput::make('email')
                            ->maxLength(255)
                            ->default(fn() => $this->email),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female'
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('total_price')
                            ->readOnly()
                            ->numeric()
                            ->default(fn() => $this->total_price)
                            ->reactive(),
                        Forms\Components\Select::make('payment_method_id')
                            ->required()
                            ->label('Payment Method')
                            ->options(
                                PaymentMethod::where('pos_transaction', true)
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->default(fn() => $this->payment_method_id)
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                if (!$state) {
                                    return;
                                }

                                $paymentMethod = PaymentMethod::find($state);
                                $isCash = $paymentMethod->is_cash ?? false;
                                $isOlshop = $paymentMethod->olshop_transaction ?? false;

                                $set('is_cash', $isCash);
                                $set('is_olshop', $isOlshop);
                                $set('payment_method_account_number', $paymentMethod->account_number);

                                if (!$isCash) {
                                    $set('paid_amount', $get('total_price'));
                                    $set('change_amount', 0);
                                } else {
                                    $set('change_amount', 0);
                                }
                            }),
                        Forms\Components\TextInput::make('payment_method_account_number')
                            ->label('Account Number')
                            ->disabled()
                            ->default(fn() => $this->payment_method_account_number)
                            ->visible(fn(Get $get): bool => $get('is_cash') !== true),
                        Forms\Components\TextInput::make('paid_amount')
                            ->numeric()
                            ->reactive()
                            ->label('Amount Paid')
                            ->readOnly(fn(Get $get) => $get('is_cash') == false)
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                $totalPrice = $get('total_price');
                                $changeAmount = $state - $totalPrice;
                                $set('change_amount', $changeAmount);
                            })
                            ->debounce(300),
                        Forms\Components\TextInput::make('change_amount')
                            ->numeric()
                            ->label('Change')
                            ->readOnly(),
                    ]),
                // Add pet information section for services that need it
                Forms\Components\Section::make('Pet Information')
                    ->visible(fn() => $this->hasServiceWithPetInfo())
                    ->schema([
                        Repeater::make('petInformation')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->label('Pet Name'),
                                TextInput::make('age')
                                    ->numeric()
                                    ->required()
                                    ->label('Pet Age'),
                                FileUpload::make('photo')
                                    ->image()
                                    ->directory('pet-photos')
                                    ->required()
                                    ->label('Pet Photo'),
                                Textarea::make('description')
                                    ->required()
                                    ->label('Pet Description'),
                                DatePicker::make('check_in')
                                    ->visible(fn() => $this->hasHotelOrBreedingService()),
                                DatePicker::make('check_out')
                                    ->visible(fn() => $this->hasHotelOrBreedingService())
                                    ->afterOrEqual('check_in'),
                                TextInput::make('days')
                                    ->readOnly()
                                    ->visible(fn() => $this->hasHotelOrBreedingService()),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                    ])
            ]);
    }

    protected function hasServiceWithPetInfo(): bool
    {
        foreach ($this->order_items as $item) {
            if (in_array($item['type'], ['grooming', 'hotel', 'breeding'])) {
                return true;
            }
        }
        return false;
    }

    protected function hasHotelOrBreedingService(): bool
    {
        foreach ($this->order_items as $item) {
            if (in_array($item['type'], ['hotel', 'breeding'])) {
                return true;
            }
        }
        return false;
    }

    public function mount()
    {
        if (session()->has('orderItems')) {
            $this->order_items = session('orderItems');
        }
        $this->payment_methods = PaymentMethod::all();
        $this->form->fill([
            'total_price' => $this->calculateTotal(),
            'payment_method_id' => $this->payment_method_id,
        ]);
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->search = ''; // Reset search when switching tabs
    }

    public function addToOrder($itemId)
    {
        switch ($this->activeTab) {
            case 'products':
                $this->addProductToOrder($itemId);
                break;
            case 'animals':
                $this->addAnimalToOrder($itemId);
                break;
            case 'grooming':
                $this->addGroomingToOrder($itemId);
                break;
            case 'hotel':
                $this->addHotelToOrder($itemId);
                break;
            case 'breeding':
                $this->addBreedingToOrder($itemId);
                break;
        }
    }

    protected function addGroomingToOrder($groomingId)
    {
        $grooming = Grooming::find($groomingId);
        if ($grooming) {
            $this->order_items[] = [
                'type' => 'grooming',
                'grooming_id' => $grooming->id,
                'name' => $grooming->name,
                'selling_price' => $grooming->selling_price,
                'thumbnail' => $grooming->photo,
                'quantity' => 1,
                'needs_pet_info' => true,
            ];

            session()->put('orderItems', $this->order_items);
            $this->calculateTotal();
            Notification::make()
                ->title('Grooming service added to order')
                ->success()
                ->send();
        }
    }

    protected function addHotelToOrder($hotelId)
    {
        $hotel = Hotel::find($hotelId);
        if ($hotel) {
            $this->order_items[] = [
                'type' => 'hotel',
                'hotel_id' => $hotel->id,
                'name' => $hotel->name,
                'selling_price' => $hotel->price_per_day,
                'thumbnail' => $hotel->thumbnail,
                'quantity' => 1,
                'needs_pet_info' => true,
            ];

            session()->put('orderItems', $this->order_items);
            $this->calculateTotal();
            Notification::make()
                ->title('Hotel service added to order')
                ->success()
                ->send();
        }
    }

    protected function addBreedingToOrder($breedingId)
    {
        $breeding = Breeding::find($breedingId);
        if ($breeding) {
            $this->order_items[] = [
                'type' => 'breeding',
                'breeding_id' => $breeding->id,
                'name' => $breeding->name,
                'selling_price' => $breeding->selling_price,
                'thumbnail' => $breeding->photo,
                'quantity' => 1,
                'needs_pet_info' => true,
            ];

            session()->put('orderItems', $this->order_items);
            $this->calculateTotal();
            Notification::make()
                ->title('Breeding service added to order')
                ->success()
                ->send();
        }
    }

    protected function addProductToOrder($productId)
    {
        $product = Product::find($productId);
        if ($product) {
            if ($product->stock <= 0) {
                Notification::make()
                    ->title('Stok habis')
                    ->danger()
                    ->send();
                return;
            }

            $existingItemKey = $this->findOrderItem($productId, 'product');

            if ($existingItemKey !== null) {
                $this->order_items[$existingItemKey]['quantity']++;
            } else {
                $this->order_items[] = [
                    'type' => 'product',
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'selling_price' => $product->selling_price,
                    'thumbnail' => $product->thumbnail,
                    'quantity' => 1,
                ];
            }

            session()->put('orderItems', $this->order_items);
            $this->calculateTotal();
            Notification::make()
                ->title('Produk ditambahkan ke keranjang')
                ->success()
                ->send();
        }
    }

    protected function addAnimalToOrder($animalId)
    {
        $animal = Animals::find($animalId);
        if ($animal) {
            if ($animal->stock <= 0) {
                Notification::make()
                    ->title('Stok hewan habis')
                    ->danger()
                    ->send();
                return;
            }

            $existingItemKey = $this->findOrderItem($animalId, 'animal');

            if ($existingItemKey !== null) {
                $this->order_items[$existingItemKey]['quantity']++;
            } else {
                $this->order_items[] = [
                    'type' => 'animal',
                    'animal_id' => $animal->id,
                    'name' => $animal->name,
                    'selling_price' => $animal->selling_price,
                    'thumbnail' => $animal->thumbnail, // Make sure this is included
                    'quantity' => 1,
                ];
            }

            session()->put('orderItems', $this->order_items);
            $this->calculateTotal();
            Notification::make()
                ->title('Hewan ditambahkan ke keranjang')
                ->success()
                ->send();
        }
    }

    protected function findOrderItem($itemId, $type)
    {
        foreach ($this->order_items as $key => $item) {
            if (
                $item['type'] === $type &&
                (($type === 'product' && $item['product_id'] == $itemId) ||
                    ($type === 'animal' && $item['animal_id'] == $itemId))
            ) {
                return $key;
            }
        }
        return null;
    }

    public function loadOrderItems($orderItems)
    {
        $this->order_items = $orderItems;
        session()->put('orderItems', $orderItems);
    }

    public function increaseQuantity($itemKey)
    {
        $item = $this->order_items[$itemKey];

        if ($item['type'] === 'product') {
            $product = Product::find($item['product_id']);
            if (!$product || $item['quantity'] + 1 > $product->stock) {
                Notification::make()
                    ->title('Stok produk tidak mencukupi')
                    ->danger()
                    ->send();
                return;
            }
        } else {
            $animal = Animals::find($item['animal_id']);
            if (!$animal || $item['quantity'] + 1 > $animal->stock) {
                Notification::make()
                    ->title('Stok hewan tidak mencukupi')
                    ->danger()
                    ->send();
                return;
            }
        }

        $this->order_items[$itemKey]['quantity']++;
        session()->put('orderItems', $this->order_items);
        $this->calculateTotal();
    }

    public function decreaseQuantity($itemKey)
    {
        if ($this->order_items[$itemKey]['quantity'] > 1) {
            $this->order_items[$itemKey]['quantity']--;
        } else {
            unset($this->order_items[$itemKey]);
            $this->order_items = array_values($this->order_items);
        }
        session()->put('orderItems', $this->order_items);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $total = 0;
        foreach ($this->order_items as $item) {
            $total += $item['quantity'] * $item['selling_price'];
        }
        $this->total_price = $total;
        $this->form->fill(['total_price' => $total]);
        return $total;
    }

    public function checkout()
    {
        $this->validate([
            'name_customer' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'gender' => 'required|in:male,female',
            'payment_method_id' => 'required',
            'paid_amount' => 'required|numeric',
            'change_amount' => 'required|numeric',
            'is_cash' => 'required|boolean',
        ]);

        $formState = $this->form->getState();

        $postransaction = PosTransaction::create([
            'name' => $this->name_customer,
            'phone' => $this->phone,
            'email' => $this->email,
            'gender' => $this->gender,
            'total_price' => $this->calculateTotal(),
            'payment_method_id' => $this->payment_method_id,
            'paid_amount' => $formState['paid_amount'],
            'change_amount' => $formState['change_amount'],
            'is_cash' => $this->is_cash,
        ]);

        foreach ($this->order_items as $item) {
            $orderData = [
                'pos_transaction_id' => $postransaction->id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['selling_price'],
                'type' => $item['type'],
            ];

            switch ($item['type']) {
                case 'product':
                    $orderData['product_id'] = $item['product_id'];
                    break;
                case 'animal':
                    $orderData['animals_id'] = $item['animal_id'];
                    break;
                case 'grooming':
                    $orderData['grooming_id'] = $item['grooming_id'];
                    break;
                case 'hotel':
                    $orderData['hotel_id'] = $item['hotel_id'];
                    break;
                case 'breeding':
                    $orderData['breeding_id'] = $item['breeding_id'];
                    break;
            }

            $order = Order::create($orderData);

            // Add pet information if this is a service that requires it
            if (in_array($item['type'], ['grooming', 'hotel', 'breeding']) && !empty($formState['petInformation'])) {
                foreach ($formState['petInformation'] as $petInfo) {
                    $petInfoData = [
                        'order_id' => $order->id,
                        'name' => $petInfo['name'],
                        'age' => $petInfo['age'],
                        'photo' => $petInfo['photo'],
                        'description' => $petInfo['description'],
                    ];

                    if (in_array($item['type'], ['hotel', 'breeding'])) {
                        $petInfoData['check_in'] = $petInfo['check_in'];
                        $petInfoData['check_out'] = $petInfo['check_out'];
                        $petInfoData['days'] = Carbon::parse($petInfo['check_in'])
                            ->diffInDays(Carbon::parse($petInfo['check_out']));
                    }

                    PetInformation::create($petInfoData);
                }
            }
        }

        $this->order_items = [];
        $this->petInformation = [];
        session()->forget('orderItems');
        $this->name_customer = '';
        $this->total_price = 0;
        $this->paid_amount = '';
        $this->payment_method_account_number = '';
        $this->gender = '';
        $this->payment_method_id = 0;

        Notification::make()
            ->title('Checkout successful!')
            ->success()
            ->send();
    }

    public function handleScanResult($decodedText)
    {
        $product = Product::where('barcode', $decodedText)->first();

        if ($product) {
            $this->addToOrder($product->id);
        } else {
            Notification::make()
                ->title('Product not found ' . $decodedText)
                ->danger()
                ->send();
        }
    }
}
