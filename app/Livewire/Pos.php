<?php

namespace App\Livewire;

use session;
use Filament\Forms;

use App\Models\Order;
use App\Models\Product;

use Filament\Forms\Get;
use Filament\Forms\Set;
use Livewire\Component;
use Filament\Forms\Form;

use App\Models\PaymentMethod;
use App\Models\PosTransaction;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;


class Pos extends Component implements HasForms
{
    use InteractsWithForms;
    public $search = '';
    public $name_customer = '';
    public $gender = '';
    public $payment_method_id = 0;
    public $payment_methods;
    public $order_items = [];
    public $total_price;
    public $paid_amount;
    public $change_amount;
    public $is_cash = 1;



    public function render()
    {
        return view('livewire.pos', [
            'products' => Product::where('stock', '>', 0)
                ->search($this->search)
                ->paginate(12)
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Form Checkout')
                    ->schema([
                        Forms\Components\TextInput::make('name_customer')
                            ->required()
                            ->maxLength(255)
                            ->default(fn() => $this->name_customer),
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
                            ->reactive(), // Tambahkan reactive() agar perubahan total_price memicu reaksi
                        Forms\Components\Select::make('payment_method_id')
                            ->required()
                            ->label('Payment Method')
                            ->options(PaymentMethod::pluck('name', 'id')->toArray())
                            ->default($this->payment_method_id)
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $paymentMethod = PaymentMethod::find($state);
                                $isCash = $paymentMethod->is_cash ?? false;
                                $set('is_cash', $isCash);

                                if (!$isCash) {
                                    // Jika is_cash = false, set paid_amount = total_price
                                    $set('paid_amount', $get('total_price'));
                                    $set('change_amount', 0);
                                } else {
                                    // Jika is_cash = true, reset paid_amount dan change_amount
                                    $set('paid_amount', 0);
                                    $set('change_amount', 0);
                                }
                            }),
                            Forms\Components\Hidden::make('is_cash')
                            ->dehydrated()
                            ->default(fn() => $this->is_cash),
                        
                        Forms\Components\TextInput::make('paid_amount')
                            ->numeric()
                            ->reactive()
                            ->label('Amount Paid')
                            ->readOnly(fn(Get $get) => $get('is_cash') == false) // Nonaktifkan jika is_cash = false
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                // Hitung change_amount ketika paid_amount diubah
                                $totalPrice = $get('total_price');
                                $changeAmount = $state - $totalPrice;
                                $set('change_amount', $changeAmount);
                            })
                            ->debounce(300), // Tambahkan debounce 300ms
                        Forms\Components\TextInput::make('change_amount')
                            ->numeric()
                            ->label('Change')
                            ->readOnly(),
                    ])
            ]);
    }

    public function mount()
    {
        if (session()->has('orderItems')) {
            $this->order_items = session('orderItems');
        }
        $this->payment_methods = PaymentMethod::all();
        $this->form->fill([
            'total_price' => $this->calculateTotal(), // Isi total_price saat mount
            'payment_method_id' => $this->payment_method_id, // Isi payment_method_id saat mount
        ]);
    }

    public function addToOrder($productId)
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

            $existingItemKey = null;
            foreach ($this->order_items as $key => $item) {
                if ($item['product_id'] == $productId) {
                    $existingItemKey = $key;
                    break;
                }
            }

            if ($existingItemKey !== null) {
                $this->order_items[$existingItemKey]['quantity']++;
            } else {
                $this->order_items[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'selling_price' => $product->selling_price,
                    'thumbnail' => $product->thumbnail,
                    'image_url' => $product->image_url,
                    'quantity' => 1,
                ];
            }

            session()->put('orderItems', $this->order_items);
            $this->calculateTotal(); // Panggil calculateTotal() untuk memperbarui total_price
            Notification::make()
                ->title('Produk ditambahkan ke keranjang')
                ->success()
                ->send();
        }
    }


    public function loadOrderItems($orderItems)
    {
        $this->order_items = $orderItems;
        session()->put('orderItems', $orderItems);
    }

    public function increaseQuantity($product_id)
    {
        $product = Product::find($product_id);

        if (!$product) {
            Notification::make()
                ->title('Produk tidak ditemukan')
                ->danger()
                ->send();
            return;
        }

        foreach ($this->order_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                if ($item['quantity'] + 1 <= $product->stock) {
                    $this->order_items[$key]['quantity']++;
                } else {
                    Notification::make()
                        ->title('Stok barang tidak mencukupi')
                        ->danger()
                        ->send();
                }
                break;
            }
        }

        session()->put('orderItems', $this->order_items);
        $this->calculateTotal(); // Panggil calculateTotal() untuk memperbarui total_price
    }


    public function decreaseQuantity($product_id)
    {
        foreach ($this->order_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                if ($this->order_items[$key]['quantity'] > 1) {
                    $this->order_items[$key]['quantity']--;
                } else {
                    unset($this->order_items[$key]);
                    $this->order_items = array_values($this->order_items);
                }
                break;
            }
        }
        session()->put('orderItems', $this->order_items);
        $this->calculateTotal(); // Panggil calculateTotal() untuk memperbarui total_price
    }

    public function calculateTotal()
    {
        $total = 0;
        foreach ($this->order_items as $item) {
            $total += $item['quantity'] * $item['selling_price'];
        }
        $this->total_price = $total;
        $this->form->fill(['total_price' => $total]); // Perbarui total_price di form
        return $total;
    }
    protected static function updateExchangePaid(Get $get, Set $set): void
    {
        $paidAmount = (int) $get('paid_amount') ?? 0;
        $totalPrice = (int) $get('total_price') ?? 0;
        $exchangePaid = $paidAmount - $totalPrice;
        $set('change_amount', $exchangePaid);
    }
    public function checkout()
    {
        // Validasi input
        $this->validate([
            'name_customer' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'payment_method_id' => 'required',
            'paid_amount' => 'required|numeric',
            'change_amount' => 'required|numeric',
            'is_cash' => 'required|boolean', // Tambah ini
        ]);

        // Ambil nilai dari form state
        $formState = $this->form->getState();

        // Buat transaksi
        $postransaction = PosTransaction::create([
            'name' => $this->name_customer,
            'gender' => $this->gender,
            'total_price' => $this->calculateTotal(),
            'payment_method_id' => $this->payment_method_id,
            'paid_amount' => $formState['paid_amount'], // Ambil nilai paid_amount dari form
            'change_amount' => $formState['change_amount'], // Ambil nilai change_amount dari form
            'is_cash' => $this->is_cash,//Tambah ini
        ]);

        // Simpan detail order ke Order
        foreach ($this->order_items as $item) {
            Order::create([
                'pos_transaction_id' => $postransaction->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['selling_price']
            ]);
        }

        // Reset order setelah checkout
        $this->order_items = [];
        session()->forget('orderItems');
        $this->name_customer = '';
        $this->gender = '';
        $this->payment_method_id = 0;

        // Kirim notifikasi sukses
        Notification::make()
            ->title('Checkout berhasil!')
            ->success()
            ->send();
    }
}