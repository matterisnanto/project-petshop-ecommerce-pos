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
use App\Models\OlshopTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OlshopTransactionResource\Pages;
use App\Filament\Resources\OlshopTransactionResource\RelationManagers;

class OlshopTransactionResource extends Resource
{
    protected static ?string $model = OlshopTransaction::class;

    protected static ?string $navigationLabel = 'Olshop Transaction';

    protected static ?string $modelLabel = 'Olshop Transaction';

    protected static ?string $pluralModelLabel = 'Olshop Transaction';

    protected static ?string $navigationGroup = 'Transactions';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationBadgeTooltip = 'Olshop transaction has not verified payment';

    public static function getNavigationBadge(): ?string
    {
        return (string) OlshopTransaction::where('is_paid', false)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

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
                        ])
                        ->afterStateHydrated(function (Get $get, Set $set) {
                            if ($get('address') && $get('weight_total') > 0) {
                                self::calculateShippingCost($get, $set);
                            }
                        }),
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
                                ->schema([
                                    // Province selection
                                    Forms\Components\Select::make('address')
                                        ->label('Address')
                                        ->required()
                                        ->live()
                                        ->searchable()
                                        ->getSearchResultsUsing(function (string $search) {
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
                                                Log::debug('Address search results', ['count' => count($data)]);

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
                                        })
                                        ->getOptionLabelUsing(function ($value) {
                                            try {
                                                $data = json_decode($value, true);
                                                return implode(', ', array_filter([
                                                    $data['subdistrict'] ?? null,
                                                    $data['district'] ?? null,
                                                    $data['city'] ?? null,
                                                    $data['province'] ?? null
                                                ]));
                                            } catch (\Exception $e) {
                                                Log::error('Error decoding address: ' . $e->getMessage());
                                                return $value;
                                            }
                                        })
                                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                            try {
                                                if ($state) {
                                                    $addressData = json_decode($state, true);
                                                    if (json_last_error() !== JSON_ERROR_NONE) {
                                                        throw new \Exception('Invalid address JSON');
                                                    }

                                                    $set('post_code', $addressData['post_code'] ?? '');
                                                    $set('_destination_id', $addressData['destination_id'] ?? null);

                                                    // Only calculate shipping if we have weight
                                                    // if (($get('weight_total') ?? 0) > 0) {
                                                    //     Log::debug('Address updated with weight, calculating shipping');
                                                    //     self::calculateShippingCost($get, $set);
                                                    // } else {
                                                    //     Log::debug('Address updated but weight is zero');
                                                    // }
                                                }
                                            } catch (\Exception $e) {
                                                Log::error('Address update error: ' . $e->getMessage());
                                                Notification::make()
                                                    ->title('Address error')
                                                    ->body($e->getMessage())
                                                    ->danger()
                                                    ->send();
                                            }
                                        })
                                        ->reactive(),
                                    Forms\Components\TextInput::make('post_code')
                                        ->label('Post Code')
                                        ->required()
                                        ->numeric()
                                        ->readOnly(),
                                    Forms\Components\TextInput::make('complete_address')
                                        ->label('Complete Address')
                                        ->required()
                                        ->columnSpanFull()
                                        ->helperText('Please fill in the complete address after postal code appears.'),
                                    Forms\Components\Hidden::make('_destination_id'),
                                ])
                                ->columns(2),
                        ]),
                    Forms\Components\Wizard\Step::make('Transaction Details')
                        ->description('')
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
                                    'jnt' => 'J&T Express',
                                    'sicepat' => 'SiCepat',
                                    'ninja' => 'Ninja Xpress',
                                    'anteraja' => 'AnterAja',
                                    'lion' => 'Lion Parcel',
                                ])
                                ->default('jne')
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

                                    // Handle existing value in edit mode
                                    $currentValue = $get('shipping_service');
                                    if ($currentValue && !array_key_exists($currentValue, $options)) {
                                        try {
                                            $data = json_decode($currentValue, true);
                                            if ($data) {
                                                // Create formatted display for existing value
                                                $formatted = self::formatShippingServiceDisplay($data);
                                                $options[$currentValue] = $formatted;
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
                                            // Ensure estimated_delivery always has a value
                                            $etd = empty($serviceData['etd']) ? '1-7 days' : $serviceData['etd'];
                                            $set('estimated_delivery', $etd);
                                        }
                                    } catch (\Exception $e) {
                                        Log::error('Error parsing shipping service: ' . $e->getMessage());
                                        // Set default values if error occurs
                                        $set('shipping_cost', 0);
                                        $set('estimated_delivery', '1-7 days');
                                    }
                                    self::calculateShippingCost($get, $set);
                                    self::updateGrandTotal($get, $set);
                                })
                                ->searchable()
                                ->columnSpanFull()
                                ->required()
                                ->helperText('Select delivery service')
                                ->reactive()
                                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                    try {
                                        $serviceData = json_decode($state, true);
                                        $etd = empty($serviceData['etd']) ? '1-7 days' : $serviceData['etd'];

                                        $set('shipping_cost', $serviceData['cost'] ?? 0);
                                        $set('estimated_delivery', $etd);
                                    } catch (\Exception $e) {
                                        Log::error('Shipping service error: ' . $e->getMessage());
                                        $set('estimated_delivery', '1-7 days');
                                    }
                                    self::updateGrandTotal($get, $set);
                                }),
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
                                // ->imagePreviewHeight('250') // tinggi preview
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
                    ->skippable()
                    ->columnSpan('full')
                    ->columns(1),

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
        return Forms\Components\Repeater::make('detail_order')
            ->relationship('orders')
            ->live()
            ->columns([
                'md' => 10,
            ])
            ->afterStateUpdated(function (Get $get, Set $set) {
                self::updateSubTotalAmount($get, $set);
                self::calculateTotalWeight($get, $set);
            })
            ->schema([
                Forms\Components\Hidden::make('type')
                    ->default('product'),
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


    // Tambahkan method ini di class OlshopTransactionResource
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

    protected static function calculateShippingCost(Get $get, Set $set): void
    {
        $address = $get('address');
        $weight = $get('weight_total') ?? 0;
        $courier = $get('courier') ?? 'jne:sicepat:ide:sap:jnt:ninja:tiki:lion:anteraja:pos:ncs:rex:rpx:sentral:star:wahana:dse';
        if (empty($address)) {
            Log::error('Address is empty!');
            $set('shipping_service_options', []);
            $set('estimated_delivery', '1-7 days'); // Add default
            return;
        }

        if ($weight <= 0) {
            Log::error('Weight must be > 0!');
            $set('shipping_service_options', []);
            $set('estimated_delivery', '1-7 days'); // Add default
            return;
        }

        try {
            $addressData = json_decode($address, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid address JSON format');
            }

            $destinationId = $addressData['destination_id'] ?? null;
            if (empty($destinationId)) {
                throw new \Exception('Destination ID not found in address data');
            }

            $apiKey = config('services.rajaongkir.key');
            $originId = config('services.rajaongkir.origin_subdistrict');

            if (empty($apiKey)) {
                throw new \Exception('RAJAONGKIR_API_KEY is not configured');
            }

            if (empty($originId)) {
                throw new \Exception('RAJAONGKIR_ORIGIN_SUBDISTRICT is not configured');
            }

            Log::debug('Shipping calculation request', [
                'origin' => $originId,
                'destination' => $destinationId,
                'weight' => $weight,
                'courier' => $courier
            ]);

            $response = Http::asForm()
                ->withHeaders(['key' => $apiKey])
                ->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                    'origin' => $originId,
                    'destination' => $destinationId,
                    'weight' => $weight,
                    'courier' => $courier,
                    'price' => 'lowest'
                ]);

            if (!$response->successful()) {
                $error = $response->json();
                Log::error('RajaOngkir API Error', $error);
                throw new \Exception($error['meta']['message'] ?? 'API request failed');
            }

            $data = $response->json();
            if (empty($data['data'])) {
                throw new \Exception('No shipping options available');
            }

            $options = collect($data['data'])->mapWithKeys(function ($service) {
                $key = json_encode([
                    'shipping_name' => $service['name'],
                    'service_name' => $service['service'],
                    'cost' => $service['cost'],
                    'etd' => $service['etd'] ?? '1-7'
                ]);

                $label = sprintf(
                    '%s - %s (Rp %s, %s)',
                    $service['name'],
                    $service['service'],
                    number_format($service['cost'], 0, ',', '.'),
                    $service['etd'] ?? '1-7 days'
                );

                return [$key => $label];
            })->toArray();

            $set('shipping_service_options', $options);

            // Auto-select first option if none selected
            if (empty($get('shipping_service')) && count($options) > 0) {
                $firstOption = array_key_first($options);
                $set('shipping_service', $firstOption);
                $serviceData = json_decode($firstOption, true);
                $set('shipping_cost', $serviceData['cost'] ?? 0);
                $set('estimated_delivery', $serviceData['etd'] ?? '1-7 days'); // Ensure default
            } else {
                $set('estimated_delivery', '1-7 days'); // Default if no options
            }
        } catch (\Exception $e) {
            Log::error('Shipping calculation failed: ' . $e->getMessage());

            $set('shipping_service_options', []);
            $set('shipping_cost', 0);

            Notification::make()
                ->title('Shipping Service Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
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

    protected static function formatShippingServiceDisplay(array $data): string
    {
        $courier = $data['courier'] ?? $data['shipping_name'] ?? 'Unknown';
        $service = $data['service'] ?? $data['service_name'] ?? 'Unknown';
        $cost = $data['cost'] ?? 0;
        $etd = empty($data['etd']) ? '1-7 days' : $data['etd'];

        // Format nama kurir jika hanya kode
        $courierNames = [
            'jne' => 'JNE',
            'tiki' => 'TIKI',
            'pos' => 'POS Indonesia',
            'jnt' => 'J&T Express',
            'sicepat' => 'SiCepat',
            'ninja' => 'Ninja Xpress',
            'anteraja' => 'AnterAja',
            'lion' => 'Lion Parcel',
        ];

        $courierDisplay = $courierNames[strtolower($courier)] ?? $courier;

        return sprintf(
            '%s - %s (Rp %s, %s)',
            $courierDisplay,
            $service,
            number_format($cost, 0, ',', '.'),
            $etd
        );
    }

    protected static function updateSubTotalAmount(Get $get, Set $set): void
    {
        $selectedProducts = collect($get('detail_order'))
            ->filter(fn($item) => !empty($item['product_id']) && !empty($item['quantity']));

        $prices = Product::find($selectedProducts->pluck('product_id'))
            ->pluck('selling_price', 'id');

        $total = $selectedProducts->reduce(function ($total, $product) use ($prices) {
            return $total + ($prices[$product['product_id']] * $product['quantity']);
        }, 0);

        $set('sub_total_amount', $total);
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
        $selectedProducts = collect($get('detail_order'))
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
            'index' => Pages\ListOlshopTransactions::route('/'),
            'create' => Pages\CreateOlshopTransaction::route('/create'),
            'edit' => Pages\EditOlshopTransaction::route('/{record}/edit'),
        ];
    }
}
