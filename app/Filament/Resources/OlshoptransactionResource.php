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

                                    // Debug the options
                                    Log::debug('Shipping Service Options:', $options);

                                    return $options;
                                })
                                ->live()
                                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                    try {
                                        $serviceData = json_decode($state, true);
                                        if (json_last_error() === JSON_ERROR_NONE) {
                                            $set('shipping_cost', $serviceData['cost'] ?? 0);
                                            $set('estimated_delivery', $serviceData['etd'] ?? '1-2');
                                        }
                                    } catch (\Exception $e) {
                                        Log::error('Error parsing shipping service data: ' . $e->getMessage());
                                    }
                                    self::updateGrandTotal($get, $set);
                                })
                                ->searchable()
                                ->columnSpanFull()
                                ->required()
                                ->helperText('Select the desired delivery service'),
                            Forms\Components\Hidden::make('estimated_delivery'),
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
            ->relationship('order')
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

        // Debug input values
        Log::debug('Shipping Cost Calculation Input:', [
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier,
        ]);

        // Reset fields
        $set('shipping_service', null);
        $set('shipping_cost', 0);
        $set('estimated_delivery', null);
        $set('shipping_service_options', []);

        // Validate required fields
        if (empty($origin)) {
            Log::error('Origin city not configured');
            return;
        }

        if (empty($destination)) {
            Log::error('Destination city not selected');
            return;
        }

        if (empty($weight)) {
            Log::error('Weight not calculated');
            return;
        }

        if (empty($courier)) {
            Log::error('Courier not selected');
            return;
        }

        try {
            $response = RajaOngkirService::getShippingCost($origin, $destination, $weight, $courier);

            // Debug raw response
            Log::debug('Raw Shipping Cost Response:', $response);

            if (empty($response['data'])) {
                Notification::make()
                    ->title('Tidak ada layanan pengiriman tersedia')
                    ->body('Kurir tidak tersedia untuk rute ini')
                    ->warning()
                    ->send();
                return;
            }

            $serviceOptions = [];

            foreach ($response['data'] as $courierData) {
                if (empty($courierData['costs'])) continue;

                foreach ($courierData['costs'] as $service) {
                    $costValue = $service['cost'][0]['value'] ?? 0;
                    $etd = $service['cost'][0]['etd'] ?? '1-2';

                    // Clean ETD
                    $etd = str_replace([' HARI', 'HARI'], '', $etd);

                    $optionValue = json_encode([
                        'courier' => $courierData['code'],
                        'service' => $service['service'],
                        'description' => $service['description'] ?? '',
                        'cost' => $costValue,
                        'etd' => $etd
                    ]);

                    $displayText = sprintf(
                        "%s %s - Rp %s (Estimasi: %s hari) %s",
                        strtoupper($courierData['code']),
                        $service['service'],
                        number_format($costValue, 0, ',', '.'),
                        $etd,
                        $service['description'] ?? ''
                    );

                    $serviceOptions[$optionValue] = $displayText;
                }
            }

            if (empty($serviceOptions)) {
                Notification::make()
                    ->title('Layanan pengiriman tidak tersedia')
                    ->body('Tidak ada layanan yang tersedia untuk kurir dan rute ini')
                    ->warning()
                    ->send();
                return;
            }

            $set('shipping_service_options', $serviceOptions);

            // Auto-select the first option
            $firstOption = array_key_first($serviceOptions);
            $serviceData = json_decode($firstOption, true);
            $set('shipping_service', $firstOption);
            $set('shipping_cost', $serviceData['cost']);
            $set('estimated_delivery', $serviceData['etd']);
        } catch (\Exception $e) {
            Log::error('Shipping cost calculation error: ' . $e->getMessage());
            Notification::make()
                ->title('Gagal menghitung ongkir')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        self::updateGrandTotal($get, $set);
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

    protected static function validateAndApplyPromoCode(Get $get, Set $set, string $promoCode): void
    {
        // Reset promo code jika input kosong
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

        // Cari promo code yang valid
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

    private static function getSelectedRegionName($id, $type): ?string
    {
        if (!$id) return null;

        $response = RajaOngkirService::getDomesticDestinations($type, $type === 'province' ? null : $id);

        if (!$response || !isset($response['data'])) {
            return null;
        }

        $keyMap = [
            'province' => 'province_id',
            'city' => 'city_id',
        ];

        $nameMap = [
            'province' => 'province',
            'city' => 'city_name',
        ];

        return collect($response['data'])
            ->firstWhere($keyMap[$type], $id)[$nameMap[$type]] ?? null;
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
