<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\PromoCode;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\PaymentMethod;
use Filament\Resources\Resource;
use App\Models\Olshoptransaction;
use App\Services\RajaOngkirService;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OlshoptransactionResource\Pages;
use App\Filament\Resources\OlshoptransactionResource\RelationManagers;

class OlshoptransactionResource extends Resource
{
    protected static ?string $model = Olshoptransaction::class;

    protected static ?string $navigationLabel = 'Olshop Transaction';

    protected static ?string $modelLabel = 'Olshop Transaction';

    protected static ?string $pluralModelLabel = 'Olshop Transaction';

    protected static ?string $navigationGroup = 'Transactions';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Product and Price')
                        ->columns(2)
                        ->label('Product and Pricing')
                        ->schema([
                            Forms\Components\Section::make('Products Ordered')
                                ->schema([
                                    self::getItemsRepeater(),
                                ]),
                            Forms\Components\TextInput::make('sub_total_amount')
                                ->required()
                                ->numeric()
                                ->prefix('Rp'),
                            Forms\Components\TextInput::make('weight_total')
                                ->label('Total Weight (gram)')
                                ->numeric()
                                ->default(0)
                                ->readOnly()
                                ->suffix(' gram'),
                            // Menempati semua kolom (full width)
                        ]),
                    Forms\Components\Wizard\Step::make('Customer Information')
                        ->columns(2)
                        ->label('Customer Information')
                        ->schema([
                            Forms\Components\Section::make('Customer Information')
                                ->description('')
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Customer Name')
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpanFull(),
                                    Forms\Components\TextInput::make('phone')
                                        ->label('Phone Number')
                                        ->tel()
                                        ->mask('999-9999-9999')
                                        ->prefix('+62')
                                        ->required()
                                        ->maxLength(255)
                                        ->dehydrateStateUsing(function ($state) {
                                            // Menambahkan prefix +62 ke data sebelum disimpan ke database
                                            return '+62' . str_replace('-', '', $state);
                                        })
                                        ->columns(1),
                                    Forms\Components\TextInput::make('email')
                                        ->label('Email Address')
                                        ->email()
                                        ->required()
                                        ->maxLength(255)
                                        ->columns(1),
                                ])
                                ->columns(2),
                            Forms\Components\Section::make('Address Information')
                                ->description('')
                                ->afterStateHydrated(function (Set $set, Get $get) {
                                    // Trigger shipping cost calculation when loading edit form
                                    self::calculateShippingCost($get, $set);
                                })
                                ->schema([
                                    Forms\Components\Select::make('province')
                                        ->label('Province')
                                        ->required()
                                        ->options(function () {
                                            $response = RajaOngkirService::getDomesticDestinations('province');
                                            return collect($response['data'])->pluck('province', 'province_id');
                                        })
                                        ->searchable()
                                        ->columnSpanFull(),
                                    Forms\Components\Select::make('city_regency')
                                        ->label('City/Regency')
                                        ->live(onBlur: true)
                                        ->required()
                                        ->options(function (callable $get) {
                                            if (!$get('province')) return [];
                                            $response = RajaOngkirService::getDomesticDestinations('city', $get('province'));

                                            return collect($response['data'])->mapWithKeys(function ($item) {
                                                // Combine type and city_name (e.g., "Kabupaten Aceh Barat")
                                                $displayName = $item['type'] . ' ' . $item['city_name'];
                                                return [$item['city_id'] => $displayName];
                                            });
                                        })
                                        ->searchable()
                                        ->searchDebounce(500)
                                        ->live() // Tambahkan ini untuk memantau perubahan
                                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                            // Ambil data kota berdasarkan city_id
                                            if (!$state) return;

                                            $response = RajaOngkirService::getDomesticDestinations('city', $get('province'));
                                            $cityData = collect($response['data'])->firstWhere('city_id', $state);

                                            if ($cityData && isset($cityData['postal_code'])) {
                                                $set('post_code', $cityData['postal_code']);
                                            }
                                        }),

                                    Forms\Components\TextInput::make('post_code')
                                        ->label('Post Code')
                                        ->required()
                                        ->numeric()
                                        ->readOnly(),
                                    Forms\Components\TextInput::make('complete_address')
                                        ->label('Complete Address')
                                        ->required()
                                        ->columnSpanFull()
                                        ->helperText('Wait until the postal code appears, then fill in the complete address to avoid repeated filling.'),
                                ])
                                ->columns(2),
                        ]),
                    Forms\Components\Wizard\Step::make('Transaction Details')
                        ->description('')
                        ->afterStateHydrated(function (Set $set, Get $get) {
                            // Trigger shipping cost calculation when loading edit form
                            self::calculateShippingCost($get, $set);
                        })
                        ->schema([
                            Forms\Components\TextInput::make('trx_id')
                                ->label('Booking Trx Number')
                                ->default('TRX-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)))
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('package_resi_number')
                                ->label('Package Resi Number')
                                ->required()
                                ->default('Being Processed')
                                ->maxLength(255),
                            Forms\Components\Select::make('courier')
                                ->label('Courier')
                                ->live(onBlur: true)
                                ->required()
                                ->options([
                                    'jne' => 'JNE',
                                    'tiki' => 'TIKI',
                                    'pos' => 'POS Indonesia',
                                ])
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::calculateShippingCost($get, $set);
                                })
                                ->columnSpanFull(),

                            Forms\Components\Select::make('shipping_service')
                                ->label('Shipping Service')
                                ->required()
                                ->options(function (Get $get) {
                                    $options = $get('shipping_service_options') ?? [];

                                    // If in edit mode and current value not in options, add it
                                    $currentValue = $get('shipping_service');
                                    if ($currentValue && !array_key_exists($currentValue, $options)) {
                                        try {
                                            $data = json_decode($currentValue, true);
                                            if ($data) {
                                                $options[$currentValue] = self::formatShippingServiceDisplay($data);
                                            }
                                        } catch (\Exception $e) {
                                            // Ignore if invalid JSON
                                        }
                                    }

                                    return $options;
                                })
                                ->getOptionLabelUsing(function ($value) {
                                    try {
                                        $data = json_decode($value, true);
                                        if ($data) {
                                            return self::formatShippingServiceDisplay($data);
                                        }
                                        return $value;
                                    } catch (\Exception $e) {
                                        return $value;
                                    }
                                })
                                ->live()
                                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                    try {
                                        $serviceData = json_decode($state, true);
                                        if ($serviceData) {
                                            $set('shipping_cost', $serviceData['cost'] ?? 0);
                                            $set('estimated_delivery', $serviceData['etd'] ?? '1-2');
                                        }
                                    } catch (\Exception $e) {
                                        Log::error('Error parsing shipping service: ' . $e->getMessage());
                                    }
                                    self::updateGrandTotal($get, $set);
                                })
                                ->searchable()
                                ->columnSpanFull()
                                ->required()
                                ->helperText('Select delivery service'),
                            Forms\Components\Hidden::make('estimated_delivery'),
                            Forms\Components\Select::make('payment_method_id')
                                ->label('Payment Method')
                                ->required()
                                ->relationship('paymentmethod', 'name')
                                ->options(function () {
                                    return PaymentMethod::where('olshop_transaction', true)
                                        ->pluck('name', 'id');
                                })
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    $paymentMethod = PaymentMethod::find($state);
                                    if ($paymentMethod) {
                                        $set('payment_method_account_number', $paymentMethod->account_number);
                                    }
                                })
                                ->columnSpanFull(),

                            Forms\Components\TextInput::make('payment_method_account_number')
                                ->label('Account Number')
                                ->readOnly()
                                ->columnSpanFull()
                                ->visible(fn(Get $get): bool => filled($get('payment_method_id'))),
                            Forms\Components\TextInput::make('promo_code_input')
                                ->label('Promo Code')
                                ->placeholder('Enter promo code')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                    self::validateAndApplyPromoCode($get, $set, $state);
                                })
                                ->columnSpanFull()
                                ->helperText(function (Get $get) {
                                    $promoId = $get('promo_code_id');
                                    if (!$promoId) return null;

                                    $promo = PromoCode::find($promoId);
                                    if (!$promo) return null;

                                    return "Promo code {$promo->code} applied (Discount: Rp " . number_format($promo->discount_amount, 0, ',', '.') . ")";
                                }),
                            Forms\Components\TextInput::make('shipping_cost')
                                ->label('Shipping Cost')
                                ->numeric()
                                ->default(0)
                                ->readOnly()
                                ->prefix('Rp'),
                            Forms\Components\TextInput::make('discount_amount')
                                ->label('Discount Amount')
                                ->required()
                                ->default(0)
                                ->readOnly()
                                ->numeric()
                                ->prefix('Rp'),
                            Forms\Components\TextInput::make('grand_total_amount')
                                ->label('Amount to be paid')
                                ->required()
                                ->numeric()
                                ->prefix('Rp')
                                ->columnSpanFull(),
                            Forms\Components\FileUpload::make('proof')
                                ->label('Proof of Payment')
                                ->image()
                                ->directory('proof-payments') // direktori penyimpanan
                                ->imagePreviewHeight('250') // tinggi preview
                                ->openable() // memungkinkan membuka gambar di tab baru
                                ->downloadable() // memungkinkan download gambar
                                ->imageEditor() // opsional: tambahkan editor gambar
                                ->panelAspectRatio('2:1') // rasio panel preview
                                ->panelLayout('integrated') // tata letak panel
                                ->required()
                                ->columnSpanFull(),
                            Forms\Components\Toggle::make('is_paid')
                                ->label('Already Paid?')
                                ->required(),
                        ])
                        ->columns(2),
                ])
                    ->columnSpan('full')
                    ->columns(1)
                    ->skippable(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trx_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('grand_total_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_paid')
                    ->boolean(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                //
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
            ->relationship('orders')
            ->live()
            ->columns([
                'md' => 10,
            ])
            ->afterStateUpdated(function (Get $get, Set $set) {
                self::updateSubTotalAmount($get, $set);
                self::calculateTotalWeight($get, $set);
                self::calculateShippingCost($get, $set);
            })
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Produk')
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
                        self::updateSubTotalAmount($get, $set);
                    })
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                Forms\Components\TextInput::make('quantity')
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
                                ->title('Stock tidak mencukupi')
                                ->warning()
                                ->send();
                        }
                        self::updateSubTotalAmount($get, $set);
                    }),

                Forms\Components\TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->readOnly()
                    ->columnSpan([
                        'md' => 1
                    ]),

                Forms\Components\TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->readOnly()
                    ->columnSpan([
                        'md' => 3
                    ]),
            ]);
    }

    protected static function calculateShippingCost(Get $get, Set $set): void
    {
        $origin = config('services.rajaongkir.origin_city');
        $destination = $get('city_regency');
        $weight = max(1, $get('weight_total'));
        $courier = $get('courier');
        $currentService = $get('shipping_service');

        // Validate required fields
        if (empty($origin) || empty($destination) || empty($weight) || empty($courier)) {
            return;
        }

        try {
            // Get fresh shipping options regardless of current service
            $response = RajaOngkirService::getShippingCost($origin, $destination, $weight, $courier);

            if (empty($response['data'])) {
                Notification::make()
                    ->title('No shipping services available')
                    ->body('Courier not available for this route')
                    ->warning()
                    ->send();
                return;
            }

            $serviceOptions = [];
            $foundCurrentService = false;

            foreach ($response['data'] as $courierData) {
                if (empty($courierData['costs'])) continue;

                foreach ($courierData['costs'] as $service) {
                    $costValue = $service['cost'][0]['value'] ?? 0;
                    $etd = str_replace([' HARI', 'HARI'], '', $service['cost'][0]['etd'] ?? '1-2');

                    $serviceData = [
                        'courier' => $courierData['code'],
                        'service' => $service['service'],
                        'description' => $service['description'] ?? '',
                        'cost' => $costValue,
                        'etd' => $etd
                    ];

                    $optionValue = json_encode($serviceData);
                    $displayText = self::formatShippingServiceDisplay($serviceData);

                    $serviceOptions[$optionValue] = $displayText;

                    // Check if this matches current service during edit
                    if ($currentService) {
                        try {
                            $currentData = json_decode($currentService, true);
                            if (
                                $currentData && $currentData['service'] === $serviceData['service']
                                && $currentData['courier'] === $serviceData['courier']
                            ) {
                                $foundCurrentService = true;
                            }
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
            }

            $set('shipping_service_options', $serviceOptions);

            // During edit, preserve the current service if it exists in new options
            if ($currentService && $foundCurrentService) {
                // No need to change the current service
            } else if (!empty($serviceOptions)) {
                // Select first option if no current service or not found
                $firstOption = array_key_first($serviceOptions);
                $serviceData = json_decode($firstOption, true);
                $set('shipping_service', $firstOption);
                $set('shipping_cost', $serviceData['cost']);
                $set('estimated_delivery', $serviceData['etd']);
            }
        } catch (\Exception $e) {
            Log::error('Shipping cost error: ' . $e->getMessage());
            Notification::make()
                ->title('Failed to calculate shipping')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        self::updateGrandTotal($get, $set);
    }

    protected static function formatShippingServiceDisplay(array $serviceData): string
    {
        return sprintf(
            "%s %s - Rp %s (Est: %s days) %s",
            strtoupper($serviceData['courier']),
            $serviceData['service'],
            number_format($serviceData['cost'], 0, ',', '.'),
            $serviceData['etd'],
            $serviceData['description'] ?? ''
        );
    }


    protected static function updateSubTotalAmount(Get $get, Set $set): void
    {
        $selectedProducts = collect($get('order'))
            ->filter(fn($item) => !empty($item['product_id']) && !empty($item['quantity']));

        $prices = Product::find($selectedProducts->pluck('product_id'))
            ->pluck('selling_price', 'id');

        $total = $selectedProducts->reduce(function ($total, $product) use ($prices) {
            return $total + ($prices[$product['product_id']] * $product['quantity']);
        }, 0);

        $set('sub_total_amount', $total);
        self::updateGrandTotal($get, $set);
    }

    // Tambahkan method ini di class OlshoptransactionResource
    protected static function applyPromoCode(Get $get, Set $set): void
    {
        $promoCodeId = $get('promo_code_id');
        $subTotal = $get('sub_total_amount') ?? 0;

        if ($promoCodeId) {
            $promoCode = \App\Models\PromoCode::find($promoCodeId);

            if ($promoCode) {
                // Set discount amount
                $set('discount_amount', $promoCode->discount_amount);

                // Beri notifikasi jika promo berhasil diterapkan
                Notification::make()
                    ->title('Promo code applied')
                    ->body('Discount: Rp ' . number_format($promoCode->discount_amount, 0, ',', '.'))
                    ->success()
                    ->send();
            } else {
                $set('discount_amount', 0);
                $set('promo_code_id', null);
            }
        } else {
            $set('discount_amount', 0);
        }

        self::updateGrandTotal($get, $set);
    }

    protected static function validateAndApplyPromoCode(Get $get, Set $set, ?string $promoCode): void
    {
        // Reset promo code if input is empty
        if (empty($promoCode)) {
            $set('discount_amount', 0);
            $set('promo_code_id', null);
            self::updateGrandTotal($get, $set);
            Notification::make()
                ->title('Promo code removed')
                ->success()
                ->send();
            return;
        }

        // Find valid promo code
        $promo = PromoCode::where('code', $promoCode)
            ->active()
            ->first();

        if ($promo) {
            $subTotal = $get('sub_total_amount') ?? 0;

            // Set discount amount
            $set('promo_code_id', $promo->id);
            $set('discount_amount', $promo->discount_amount);

            Notification::make()
                ->title('Promo code applied successfully')
                ->body('Discount: Rp ' . number_format($promo->discount_amount, 0, ',', '.'))
                ->success()
                ->send();
        } else {
            // Invalid promo code
            $set('promo_code_id', null);
            $set('discount_amount', 0);

            Notification::make()
                ->title('Invalid promo code')
                ->body('The promo code is not valid or has expired')
                ->danger()
                ->send();
        }

        self::updateGrandTotal($get, $set);
    }

    protected static function updateGrandTotal(Get $get, Set $set): void
    {
        $subTotal = $get('sub_total_amount') ?? 0;
        $discount = $get('discount_amount') ?? 0;
        $shipping = $get('shipping_cost') ?? 0;

        // Pastikan diskon tidak melebihi subtotal
        $effectiveDiscount = min($discount, $subTotal);

        $grandTotal = $subTotal - $effectiveDiscount + $shipping;

        $set('grand_total_amount', max(0, $grandTotal));
    }

    protected static function calculateTotalWeight(Get $get, Set $set): void
    {
        $selectedProducts = collect($get('order'))
            ->filter(fn($item) => !empty($item['product_id']) && !empty($item['quantity']));

        $products = Product::find($selectedProducts->pluck('product_id'));

        $totalWeight = 0;

        foreach ($selectedProducts as $item) {
            $product = $products->firstWhere('id', $item['product_id']);
            $totalWeight += ($product->weight ?? 0) * $item['quantity'];
        }

        $set('weight_total', $totalWeight);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOlshoptransactions::route('/'),
            'create' => Pages\CreateOlshoptransaction::route('/create'),
            // 'edit' => Pages\EditOlshoptransaction::route('/{record}/edit'),
        ];
    }
}
