<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;

use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PaymentMethod;
use App\Models\PosTransaction;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Filament\Resources\PostransactionResource\Pages;
use App\Models\Animals;

class PostransactionResource extends Resource
{
    protected static ?string $model = PosTransaction::class;
    protected static ?string $navigationLabel = 'POS Transaction';
    protected static ?string $modelLabel = 'POS Transaction';
    protected static ?string $pluralModelLabel = 'POS Transactions';
    protected static ?string $navigationGroup = 'Transactions';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->columnSpanFull() // Tambahkan ini
                    ->schema([
                        Forms\Components\Section::make('Main Information')
                            ->schema([
                                Forms\Components\TextInput::make('name')->maxLength(255),
                                Forms\Components\TextInput::make('email')->email()->maxLength(255)->default(null),
                                Forms\Components\Select::make('gender')
                                    ->options([
                                        'male' => 'Male',
                                        'female' => 'Female',
                                    ])
                                    ->required(),
                            ])
                            ->columnSpanFull(),
                        Section::make('Ordered Items')
                            ->schema([
                                Repeater::make('order')
                                    ->relationship()
                                    ->live()
                                    ->statePath('orderItems') // Pastikan ini ada
                                    ->columns(['md' => 10])
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::updateTotalPrice($get, $set);
                                    })
                                    ->schema([
                                        Select::make('type')
                                            ->options([
                                                'product' => 'Product',
                                                'animal' => 'Animal',
                                            ])
                                            ->required()
                                            ->live() // Tambahkan live()
                                            ->columnSpan(2)
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $set('product_id', null);
                                                $set('animals_id', null);
                                                $set('unit_price', 0);
                                                $set('quantity', 1);
                                                self::updateTotalPrice($get, $set); // Panggil updateTotalPrice
                                            }),

                                        Select::make('product_id')
                                            ->label('Product')
                                            ->live(debounce: 500)
                                            ->options(Product::query()->where('stock', '>', 0)->pluck('name', 'id'))
                                            ->columnSpan(4)
                                            ->required(fn(Get $get): bool => $get('type') === 'product')
                                            ->visible(fn(Get $get): bool => $get('type') === 'product') // Tambahkan live()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                if ($state) {
                                                    $product = Product::find($state);
                                                    $set('unit_price', $product->selling_price ?? 0);
                                                    $set('stock', $product->stock ?? 0);
                                                }
                                                self::updateTotalPrice($get, $set); // Panggil updateTotalPrice
                                            }),

                                        Select::make('animals_id')
                                            ->label('Animal')
                                            ->options(Animals::query()->where('is_active', true)->where('stock', '>', 0)->pluck('name', 'id'))
                                            ->columnSpan(4)
                                            ->required(fn(Get $get): bool => $get('type') === 'animal')
                                            ->visible(fn(Get $get): bool => $get('type') === 'animal')
                                            ->live() // Tambahkan live()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                if ($state) {
                                                    $animal = Animals::find($state);
                                                    $set('unit_price', $animal->selling_price ?? 0);
                                                    $set('stock', $animal->stock ?? 0);
                                                }
                                                self::updateTotalPrice($get, $set); // Panggil updateTotalPrice
                                            }),

                                        TextInput::make('quantity')
                                            ->required()
                                            ->numeric()
                                            ->default(1)
                                            ->minValue(1)
                                            ->columnSpan(1)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                if ($get('type') === 'product') {
                                                    $stock = $get('stock');
                                                    if ($state > $stock) {
                                                        $set('quantity', $stock);
                                                        Notification::make()
                                                            ->title('Insufficient stock')
                                                            ->warning()
                                                            ->send();
                                                    }
                                                } elseif ($get('type') === 'animal') {
                                                    $stock = $get('stock');
                                                    if ($state > $stock) {
                                                        $set('quantity', $stock);
                                                        Notification::make()
                                                            ->title('Insufficient animal stock')
                                                            ->warning()
                                                            ->send();
                                                    }
                                                }
                                                self::updateTotalPrice($get, $set);
                                            }),
                                        TextInput::make('stock')
                                            ->numeric()
                                            ->readOnly()
                                            ->columnSpan(1)
                                            ->visible(fn(Get $get): bool => $get('type') === 'product'),

                                        TextInput::make('unit_price')
                                            ->required()
                                            ->numeric()
                                            ->readOnly()
                                            ->columnSpan(2)
                                            ->live(),
                                    ]),
                            ]),
                        Forms\Components\Group::make()
                            ->columns(2) // Membuat layout 2 kolom
                            ->schema([
                                // Kolom pertama: Total Price & Note
                                Forms\Components\Section::make('Total & Notes')
                                    ->schema([
                                        Forms\Components\TextInput::make('total_price')
                                            ->required()
                                            ->numeric()
                                            ->readOnly(),
                                        Forms\Components\Textarea::make('note')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpan(1), // Ambil 1 kolom dari 2 kolom total

                                // Kolom kedua: Pembayaran
                                Forms\Components\Section::make('Payment')
                                    ->schema([
                                        Forms\Components\Select::make('payment_method_id')
                                            ->relationship('paymentMethod', 'name')
                                            ->reactive()
                                            ->options(function () {
                                                return PaymentMethod::where('pos_transaction', true) // Filter yang pos_transaction = true
                                                    ->pluck('name', 'id');
                                            })
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $paymentMethod = PaymentMethod::find($state);
                                                $set('is_cash', $paymentMethod->is_cash ?? false);

                                                if (!$paymentMethod->is_cash) {
                                                    $set('change_amount', 0);
                                                    $set('paid_amount', $get('total_price'));
                                                }
                                            })
                                            ->afterStateHydrated(function (Set $set, Get $get, $state) {
                                                $paymentMethod = PaymentMethod::find($state);
                                                if (!$paymentMethod?->is_cash) {
                                                    $set('paid_amount', $get('total_price'));
                                                    $set('change_amount', 0);
                                                }

                                                $set('is_cash', $paymentMethod->is_cash ?? false);
                                            })
                                            ->live(),
                                        Forms\Components\Hidden::make('is_cash')
                                            ->dehydrated(),
                                        Forms\Components\TextInput::make('paid_amount')
                                            ->numeric()
                                            ->reactive()
                                            ->live(debounce: 1000)
                                            ->label('Amount Paid')
                                            ->readOnly(fn(Get $get) => $get('is_cash') == false)
                                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                                // Function untuk menghitung uang kembalian
                                                self::updateExchangePaid($get, $set);
                                            }),
                                        Forms\Components\TextInput::make('change_amount')
                                            ->numeric()
                                            ->label('Change')
                                            ->readOnly(),
                                    ])
                                    ->columnSpan(1), // Ambil 1 kolom dari 2 kolom total
                            ])

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('gender'),
                Tables\Columns\TextColumn::make('total_price')->numeric()->sortable(),

                // Kolom ini untuk menampilkan nama metode pembayaran
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label('Payment Method')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('paid_amount')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('change_amount')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function updateTotalPrice(Get $get, Set $set): void
    {
        $total = 0;
        $orders = $get('orderItems') ?? [];

        foreach ($orders as $order) {
            if (!empty($order['product_id'])) {
                // Jika ada product_id, dapatkan harga langsung dari database
                $product = Product::find($order['product_id']);
                $price = $product->selling_price ?? 0;
                $quantity = (int) ($order['quantity'] ?? 1);
                $total += $price * $quantity;
            } elseif (!empty($order['animals_id'])) {
                // Sama untuk animals
                $animal = Animals::find($order['animals_id']);
                $price = $animal->selling_price ?? 0;
                $quantity = (int) ($order['quantity'] ?? 1);
                $total += $price * $quantity;
            }
        }

        $set('total_price', $total);

        if (!$get('is_cash')) {
            $set('paid_amount', $total);
            $set('change_amount', 0);
        }
    }

    protected static function updateExchangePaid(Get $get, Set $set): void
    {
        $paidAmount = (int) $get('paid_amount') ?? 0;
        $totalPrice = (int) $get('total_price') ?? 0;
        $exchangePaid = $paidAmount - $totalPrice;
        $set('change_amount', $exchangePaid);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPostransactions::route('/'),
            'create' => Pages\CreatePostransaction::route('/create'),
            // 'edit' => Pages\EditPostransaction::route('/{record}/edit'),
        ];
    }
}
