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

class PostransactionResource extends Resource
{
    protected static ?string $model = PosTransaction::class;
    protected static ?string $navigationLabel = 'POS Transaction';
    protected static ?string $modelLabel = 'POS Transaction';
    protected static ?string $pluralModelLabel = 'POS Transactions';
    protected static ?string $navigationGroup = 'Transactions';
    protected static ?int $navigationSort = 0;
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
                                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                                Forms\Components\TextInput::make('email')->email()->maxLength(255)->default(null),
                                Forms\Components\Select::make('gender')
                                    ->options([
                                        'male' => 'Male',
                                        'female' => 'Female',
                                    ])
                                    ->required(),
                            ])
                            ->columnSpanFull(),
                        Forms\Components\Section::make('Ordered Products')
                            ->schema([
                                self::getItemsRepeater(),
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
                                            }),
                                        Forms\Components\Hidden::make('is_cash')
                                            ->dehydrated(),
                                        Forms\Components\TextInput::make('paid_amount')
                                            ->numeric()
                                            ->reactive()
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

    public static function getItemsRepeater(): Repeater
    {
        return Forms\Components\Repeater::make('order')
            ->relationship('order')
            ->live()
            ->columns([
                'md' => 10,
            ])
            ->afterStateUpdated(function (Get $get, Set $set) {
                self::updateTotalPrice($get, $set);
            })
            ->schema([
                Select::make('product_id')
                    ->label('Product')
                    ->required()
                    ->options(Product::query()->where('stock', '>', 1)->pluck('name', 'id'))
                    ->columnSpan([
                        'md' => 5,
                    ])
                    ->afterStateHydrated(function (Set $set, Get $get, $state) {
                        $product = Product::find($state);
                        $set('unit_price', $product->selling_price ?? 0);
                        $set('stock', $product->stock ?? 0);
                    })
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        $product = Product::find($state);
                        $set('unit_price', $product->selling_price ?? 0);
                        $set('stock', $product->stock ?? 0);
                        $quantity = $get('quantity') ?? 1;
                        $stock = $get('stock');
                        self::updateTotalPrice($get, $set);
                    })
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->columnSpan([
                        'md' => 1
                    ])
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        $stock = $get('stock');
                        if ($state > $stock) {
                            $set('quantity', $stock);
                            Notification::make()
                                ->title('Insufficient stock')
                                ->warning()
                                ->send();
                        }
                        self::updateTotalPrice($get, $set);
                    }),

                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->readOnly()
                    ->columnSpan([
                        'md' => 1
                    ]),

                TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->readOnly()
                    ->columnSpan([
                        'md' => 3
                    ]),
            ]);
    }

    protected static function updateTotalPrice(Get $get, Set $set): void
    {
        $selectedProducts = collect($get('order'))
            ->filter(fn($item) => !empty($item['product_id']) && !empty($item['quantity']));

        $prices = Product::find($selectedProducts->pluck('product_id'))
            ->pluck('selling_price', 'id');

        $total = $selectedProducts->reduce(function ($total, $product) use ($prices) {
            return $total + ($prices[$product['product_id']] * $product['quantity']);
        }, 0);

        $set('total_price', $total);
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
