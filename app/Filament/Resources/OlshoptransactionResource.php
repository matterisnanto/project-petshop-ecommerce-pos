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
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OlshoptransactionResource\Pages;
use App\Filament\Resources\OlshoptransactionResource\RelationManagers;

class OlshoptransactionResource extends Resource
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
                                            return collect($response['data'])
                                                ->mapWithKeys(function ($item) {
                                                    $id = $item['province_id'] ?? $item['id'];
                                                    $name = $item['province'] ?? $item['name'];
                                                    return [$id => $name];
                                                });
                                        })
                                        ->searchable()
                                        ->columnSpanFull()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, Set $set) {
                                            $set('city_regency', null);
                                            $set('district', null);
                                            $set('sub_district', null);
                                            $set('post_code', null);
                                        }),

                                    // City/Regency Select
                                    Forms\Components\Select::make('city_regency')
                                        ->label('City/Regency')
                                        ->required()
                                        ->options(function (Get $get) {
                                            $provinceId = $get('province');
                                            if (!$provinceId) return [];

                                            $response = RajaOngkirService::getDomesticDestinations('city', $provinceId);
                                            return collect($response['data'])->mapWithKeys(function ($item) {
                                                $cityId = $item['city_id'] ?? $item['id'];
                                                $type = $item['type'] ?? '';
                                                $cityName = $item['city_name'] ?? $item['name'];
                                                $displayName = $type ? "{$type} {$cityName}" : $cityName;
                                                return [$cityId => $displayName];
                                            });
                                        })
                                        ->searchable()
                                        ->live()
                                        ->afterStateUpdated(function ($state, Set $set) {
                                            $set('district', null);
                                            $set('sub_district', null);
                                            $set('post_code', null);
                                        })
                                        ->disabled(fn(Get $get) => !$get('province'))
                                        ->helperText(fn(Get $get) => !$get('province') ? 'Please select a province first' : ''),

                                    // District Select
                                    Forms\Components\Select::make('district')
                                        ->label('District')
                                        ->required()
                                        ->options(function (Get $get) {
                                            $cityId = $get('city_regency');
                                            if (!$cityId) return [];

                                            $response = RajaOngkirService::getDomesticDestinations('district', $cityId);
                                            return collect($response['data'])->mapWithKeys(function ($item) {
                                                $districtId = $item['id'];
                                                $districtName = $item['name'];
                                                return [$districtId => $districtName];
                                            });
                                        })
                                        ->searchable()
                                        ->live()
                                        ->afterStateUpdated(function ($state, Set $set) {
                                            $set('sub_district', null);
                                            $set('post_code', null);
                                        })
                                        ->disabled(fn(Get $get) => !$get('city_regency'))
                                        ->helperText(fn(Get $get) => !$get('city_regency') ? 'Please select a city first' : ''),

                                    // Subdistrict Select (with postal code extraction)
                                    Forms\Components\Select::make('sub_district')
                                        ->label('Subdistrict')
                                        ->required()
                                        ->options(function (Get $get) {
                                            $districtId = $get('district');
                                            if (!$districtId) return [];

                                            $response = RajaOngkirService::getDomesticDestinations('subdistrict', $districtId);
                                            return collect($response['data'])->mapWithKeys(function ($item) {
                                                $subdistrictId = $item['id'];
                                                $subdistrictName = $item['name'];
                                                $zipCode = $item['zip_code'] ?? '';
                                                return [$subdistrictId => "{$subdistrictName}"];
                                            });
                                        })
                                        ->searchable()
                                        ->live()
                                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                            if (!$state) {
                                                $set('post_code', null);
                                                return;
                                            }

                                            try {
                                                $districtId = $get('district');
                                                $response = RajaOngkirService::getDomesticDestinations('subdistrict', $districtId);

                                                $subdistrictData = collect($response['data'])->firstWhere('id', $state);

                                                if ($subdistrictData && !empty($subdistrictData['zip_code']) && $subdistrictData['zip_code'] != "0") {
                                                    $set('post_code', $subdistrictData['zip_code']);
                                                } else {
                                                    $set('post_code', null);
                                                }
                                            } catch (\Exception $e) {
                                                Log::error('Failed to update postal code: ' . $e->getMessage());
                                                $set('post_code', null);
                                            }
                                        })
                                        ->disabled(fn(Get $get) => !$get('district'))
                                        ->helperText(fn(Get $get) => !$get('district') ? 'Please select a district first' : ''),

                                    // Postal Code Input
                                    Forms\Components\TextInput::make('post_code')
                                        ->label('Postal Code')
                                        ->required()
                                        ->numeric()
                                        ->readOnly()
                                        ->dehydrated()
                                        ->maxLength(5)
                                        ->helperText('Postal code is automatically filled based on subdistrict selection'),
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
                                ->label('Kurir')
                                ->live()
                                ->required()
                                ->options([
                                    'jne' => 'JNE',
                                    'tiki' => 'TIKI',
                                    'pos' => 'POS Indonesia',
                                    'sicepat' => 'SiCepat',
                                    'jnt' => 'J&T',
                                    'anteraja' => 'AnterAja',
                                    'ninja' => 'Ninja Xpress',
                                    'ide' => 'ID Express',
                                    'rex' => 'REX',
                                    'sap' => 'SAP',
                                    'ncs' => 'NCS',
                                ])
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    $set('shipping_service', null);
                                    $set('shipping_cost', 0);
                                    self::calculateShippingCost($get, $set);
                                })
                                ->columnSpanFull(),

                            Forms\Components\Select::make('shipping_service')
                                ->label('Layanan Pengiriman')
                                ->required()
                                ->options(function (Get $get) {
                                    $options = $get('shipping_service_options') ?? [];

                                    // Handle edit mode
                                    $currentValue = $get('shipping_service');
                                    if ($currentValue && !isset($options[$currentValue])) {
                                        try {
                                            $currentData = json_decode($currentValue, true);
                                            if ($currentData) {
                                                $options[$currentValue] = self::formatShippingServiceDisplay($currentData);
                                            }
                                        } catch (\Exception $e) {
                                            Log::debug('Failed to parse current shipping service', ['error' => $e->getMessage()]);
                                        }
                                    }

                                    return $options;
                                })
                                ->getOptionLabelUsing(function ($value) {
                                    try {
                                        $data = json_decode($value, true);
                                        return $data ? self::formatShippingServiceDisplay($data) : $value;
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
                                            $set('estimated_delivery', $serviceData['etd'] ?? '');

                                            // Sync courier code
                                            if ($get('courier') !== $serviceData['code']) {
                                                $set('courier', $serviceData['code']);
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        Log::error('Error processing shipping service: ' . $e->getMessage());
                                    }
                                    self::updateGrandTotal($get, $set);
                                })
                                ->searchable()
                                ->columnSpanFull()
                                ->required()
                                ->helperText(function (Get $get) {
                                    $service = $get('shipping_service');
                                    if (!$service) return 'Pilih layanan pengiriman';

                                    try {
                                        $data = json_decode($service, true);
                                        if ($data) {
                                            $etd = str_replace([' day', ' days'], '', $data['etd'] ?? '');
                                            return "{$data['service']} | Estimasi: " . ($etd ?: '-') . " hari";
                                        }
                                    } catch (\Exception $e) {
                                        return 'Info layanan tidak tersedia';
                                    }
                                }),
                            Forms\Components\Hidden::make('shipping_service_options'),
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
                self::calculateShippingCost($get, $set);
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

    protected static function calculateShippingCost(Get $get, Set $set): void
    {
        $origin = config('services.rajaongkir.origin_subdistrict');
        $destination = $get('sub_district');
        $weight = max(1, $get('weight_total'));
        $courier = $get('courier');

        // Validasi field wajib
        if (empty($origin) || empty($destination) || empty($weight) || empty($courier)) {
            Notification::make()
                ->title('Data Tidak Lengkap')
                ->body('Harap lengkapi alamat lengkap dan pilih kurir')
                ->warning()
                ->send();
            return;
        }

        try {
            $response = RajaOngkirService::getShippingCost($origin, $destination, $weight, $courier);

            if (empty($response['data'])) {
                Notification::make()
                    ->title('Layanan Tidak Tersedia')
                    ->body('Kurir tidak tersedia untuk rute ini')
                    ->warning()
                    ->send();
                return;
            }

            // Format opsi layanan
            $serviceOptions = [];
            foreach ($response['data'] as $service) {
                $serviceData = [
                    'code' => $service['code'],
                    'service' => $service['service'],
                    'description' => $service['description'] ?? '',
                    'cost' => $service['cost'],
                    'etd' => $service['etd']
                ];

                $optionValue = json_encode($serviceData);
                $serviceOptions[$optionValue] = self::formatShippingServiceDisplay($serviceData);
            }

            // Urutkan berdasarkan harga termurah
            uasort($serviceOptions, function ($a, $b) {
                preg_match('/Rp (\d+\.?\d*)/', $a, $matchesA);
                preg_match('/Rp (\d+\.?\d*)/', $b, $matchesB);
                $priceA = (float) str_replace('.', '', $matchesA[1] ?? 0);
                $priceB = (float) str_replace('.', '', $matchesB[1] ?? 0);
                return $priceA <=> $priceB;
            });

            $set('shipping_service_options', $serviceOptions);

            // Auto-select cheapest option
            if (!empty($serviceOptions) && !$get('shipping_service')) {
                $firstOption = array_key_first($serviceOptions);
                $serviceData = json_decode($firstOption, true);
                $set('shipping_service', $firstOption);
                $set('shipping_cost', $serviceData['cost']);
                $set('estimated_delivery', $serviceData['etd']);
            }
        } catch (\Exception $e) {
            Log::error('Shipping Calculation Error: ' . $e->getMessage(), [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier
            ]);

            Notification::make()
                ->title('Gagal Menghitung Ongkir')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        self::updateGrandTotal($get, $set);
    }

    protected static function formatShippingServiceDisplay(array $serviceData): string
    {
        $etd = str_replace([' day', ' days'], '', $serviceData['etd'] ?? '');
        $etdDisplay = !empty($etd) ? "Estimasi: {$etd} hari" : "Estimasi: -";

        $courierName = match ($serviceData['code']) {
            'jne' => 'JNE',
            'tiki' => 'TIKI',
            'pos' => 'POS',
            'sicepat' => 'SiCepat',
            'jnt' => 'J&T',
            'anteraja' => 'AnterAja',
            'ninja' => 'Ninja Xpress',
            'ide' => 'ID Express',
            'rex' => 'REX',
            'sap' => 'SAP',
            'ncs' => 'NCS',
            default => strtoupper($serviceData['code'])
        };

        return sprintf(
            "%s %s - Rp %s | %s%s",
            $courierName,
            $serviceData['service'],
            number_format($serviceData['cost'], 0, ',', '.'),
            $etdDisplay,
            $serviceData['description'] ? " ({$serviceData['description']})" : ''
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
            'index' => Pages\ListOlshoptransactions::route('/'),
            'create' => Pages\CreateOlshoptransaction::route('/create'),
            // 'edit' => Pages\EditOlshoptransaction::route('/{record}/edit'),
        ];
    }
}
