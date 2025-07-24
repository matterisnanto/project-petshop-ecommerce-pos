<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Hotel;

use App\Models\Animals;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Breeding;
use App\Models\Grooming;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\PaymentMethod;
use App\Models\POSTransaction;
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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use App\Filament\Resources\PostransactionResource\Pages;
use Illuminate\Database\Eloquent\Builder;

class PostransactionResource extends Resource
{
    protected static ?string $model = PosTransaction::class;

    protected static ?string $navigationLabel = 'POS Transaction';

    protected static ?string $modelLabel = 'POS Transaction';

    protected static ?string $pluralModelLabel = 'POS Transactions';

    protected static ?string $navigationGroup = 'Transactions';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationBadgeTooltip = 'POS transaction today';

    public static function getNavigationBadge(): ?string
    {
        return (string) PosTransaction::whereDate('created_at', today())->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return (string) PosTransaction::whereDate('created_at', today())->count() < 0 ? 'success' : 'danger';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Section::make('Main Information')
                            ->schema([
                                Forms\Components\Hidden::make('trx_id')
                                    ->default('TRX-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6))),
                                Forms\Components\TextInput::make('name')->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->mask('999-9999-9999')
                                    ->prefix('+62')
                                    ->maxLength(255)
                                    ->dehydrateStateUsing(function ($state) {
                                        // Menambahkan prefix +62 ke data sebelum disimpan ke database
                                        return '+62' . str_replace('-', '', $state);
                                    }),
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
                                Repeater::make('detail_order')
                                    ->relationship()
                                    ->live()
                                    ->statePath('orderItems')
                                    ->columns(['md' => 10])
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::updateTotalPrice($get, $set);
                                    })
                                    ->schema([
                                        Select::make('type')
                                            ->options([
                                                'product' => 'Product',
                                                'animal' => 'Animal',
                                                'grooming' => 'Grooming',
                                                'hotel' => 'Hotel',
                                                'breeding' => 'Breeding',
                                            ])
                                            ->required()
                                            ->live()
                                            ->columnSpan(2)
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $set('product_id', null);
                                                $set('animals_id', null);
                                                $set('grooming_id', null);
                                                $set('hotel_id', null);
                                                $set('unit_price', 0);
                                                $set('quantity', 1);
                                                self::updateTotalPrice($get, $set);
                                            }),

                                        Select::make('product_id')
                                            ->label('Product')
                                            ->live(debounce: 500)
                                            ->options(Product::query()->where('stock', '>', 0)->pluck('name', 'id'))
                                            ->columnSpan(4)
                                            ->required(fn(Get $get): bool => $get('type') === 'product')
                                            ->visible(fn(Get $get): bool => $get('type') === 'product')
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                if ($state) {
                                                    $product = Product::find($state);
                                                    $set('unit_price', $product->selling_price ?? 0);
                                                    $set('stock', $product->stock ?? 0);
                                                }
                                                self::updateTotalPrice($get, $set);
                                            }),

                                        Select::make('animals_id')
                                            ->label('Animal')
                                            ->options(Animals::query()->where('is_active', true)->where('stock', '>', 0)->pluck('name', 'id'))
                                            ->columnSpan(4)
                                            ->required(fn(Get $get): bool => $get('type') === 'animal')
                                            ->visible(fn(Get $get): bool => $get('type') === 'animal')
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                if ($state) {
                                                    $animal = Animals::find($state);
                                                    $set('unit_price', $animal->selling_price ?? 0);
                                                    $set('stock', $animal->stock ?? 0);
                                                }
                                                self::updateTotalPrice($get, $set);
                                            }),

                                        Select::make('grooming_id')
                                            ->label('Grooming Service')
                                            ->options(Grooming::query()->where('is_active', true)->pluck('name', 'id'))
                                            ->columnSpan(4)
                                            ->required(fn(Get $get): bool => $get('type') === 'grooming')
                                            ->visible(fn(Get $get): bool => $get('type') === 'grooming')
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                if ($state) {
                                                    $grooming = Grooming::find($state);
                                                    $set('unit_price', $grooming->selling_price ?? 0);
                                                }
                                                self::updateTotalPrice($get, $set);
                                            }),

                                        Select::make('hotel_id')
                                            ->label('Hotel Service')
                                            ->options(Hotel::query()->where('is_active', true)->pluck('name', 'id'))
                                            ->columnSpan(4)
                                            ->required(fn(Get $get): bool => $get('type') === 'hotel')
                                            ->visible(fn(Get $get): bool => $get('type') === 'hotel')
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                if ($state) {
                                                    $hotel = Hotel::find($state);
                                                    $set('unit_price', $hotel->price_per_day ?? 0);
                                                }
                                                self::updateTotalPrice($get, $set);
                                            }),

                                        Select::make('breeding_id')
                                            ->label('Breeding Service')
                                            ->options(Breeding::query()->where('is_active', true)->pluck('name', 'id'))
                                            ->columnSpan(4)
                                            ->required(fn(Get $get): bool => $get('type') === 'breeding')
                                            ->visible(fn(Get $get): bool => $get('type') === 'breeding')
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                if ($state) {
                                                    $breeding = Breeding::find($state);
                                                    $set('unit_price', $breeding->selling_price ?? 0);
                                                }
                                                self::updateTotalPrice($get, $set);
                                            }),

                                        TextInput::make('quantity')
                                            ->required()
                                            ->numeric()
                                            ->default(1)
                                            ->minValue(1)
                                            ->columnSpan(1)
                                            ->live(debounce: 1000)
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

                                        // Pet Information Section (visible only for grooming and hotel)
                                        Repeater::make('petInformation')
                                            ->relationship()
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
                                                DatePicker::make('check_in'),
                                                DatePicker::make('check_out')
                                                    ->afterOrEqual('check_in'),
                                                TextInput::make('days')
                                                    ->readOnly(),
                                            ])
                                            ->columnSpanFull()
                                            ->visible(fn(Get $get): bool => in_array($get('type'), ['grooming', 'hotel', 'breeding']))
                                    ]),
                            ]),
                        Forms\Components\Group::make()
                            ->columns(2)
                            ->schema([
                                Forms\Components\Section::make('Total & Notes')
                                    ->schema([
                                        Forms\Components\TextInput::make('total_price')
                                            ->required()
                                            ->numeric()
                                            ->readOnly(),
                                        Forms\Components\Textarea::make('note')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpan(1),

                                Forms\Components\Section::make('Payment')
                                    ->schema([
                                        Forms\Components\Select::make('payment_method_id')
                                            ->relationship('paymentMethod', 'name')
                                            ->reactive()
                                            ->options(function () {
                                                return PaymentMethod::where('pos_transaction', true)
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
                                                self::updateExchangePaid($get, $set);
                                            }),
                                        Forms\Components\TextInput::make('change_amount')
                                            ->numeric()
                                            ->label('Change')
                                            ->readOnly(),
                                    ])
                                    ->columnSpan(1),
                            ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trx_id')->searchable(),
                Tables\Columns\TextColumn::make('gender'),
                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()
                    ->sortable()
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label('Payment Method')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->numeric()
                    ->sortable()
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('change_amount')
                    ->numeric()
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Date filter
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('date_from'),
                        DatePicker::make('date_to'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                // Month filter
                Tables\Filters\SelectFilter::make('month')
                    ->options([
                        '01' => 'January',
                        '02' => 'February',
                        '03' => 'March',
                        '04' => 'April',
                        '05' => 'May',
                        '06' => 'June',
                        '07' => 'July',
                        '08' => 'August',
                        '09' => 'September',
                        '10' => 'October',
                        '11' => 'November',
                        '12' => 'December',
                    ])
                    ->query(function (Builder $query, $data) {
                        if ($data['value']) {
                            $query->whereMonth('created_at', $data['value']);
                        }
                    }),

                // Payment method filter
                Tables\Filters\SelectFilter::make('payment_method')
                    ->relationship('paymentMethod', 'name')
                    ->searchable()
                    ->preload(),
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

        foreach ($orders as $detail_order) {
            if (!empty($detail_order['product_id'])) {
                $product = Product::find($detail_order['product_id']);
                $price = $product->selling_price ?? 0;
                $quantity = (int) ($detail_order['quantity'] ?? 1);
                $total += $price * $quantity;
            } elseif (!empty($detail_order['animals_id'])) {
                $animal = Animals::find($detail_order['animals_id']);
                $price = $animal->selling_price ?? 0;
                $quantity = (int) ($detail_order['quantity'] ?? 1);
                $total += $price * $quantity;
            } elseif (!empty($detail_order['grooming_id'])) {
                $grooming = Grooming::find($detail_order['grooming_id']);
                $price = $grooming->selling_price ?? 0;
                $quantity = (int) ($detail_order['quantity'] ?? 1);
                $total += $price * $quantity;
            } elseif (!empty($detail_order['hotel_id'])) {
                $hotel = Hotel::find($detail_order['hotel_id']);
                $price = $hotel->price_per_day ?? 0;
                $quantity = (int) ($detail_order['quantity'] ?? 1);
                $total += $price * $quantity;
            } elseif (!empty($detail_order['breeding_id'])) {
                $breeding = Breeding::find($detail_order['breeding_id']);
                $price = $breeding->selling_price ?? 0;
                $quantity = (int) ($detail_order['quantity'] ?? 1);
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
