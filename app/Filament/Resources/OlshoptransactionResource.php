<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\Olshoptransaction;
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
                            Forms\Components\Section::make('Price to pay')
                                ->schema([
                                    Forms\Components\TextInput::make('sub_total_amount')
                                        ->required()
                                        ->numeric()
                                        ->columnSpanFull(),
                                    Forms\Components\Select::make('promo_code_id')
                                        ->label('Promo Code')
                                        ->relationship('promocode', 'code')
                                        ->default(null)
                                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                            // Logika untuk menghitung diskon berdasarkan promo code
                                            $discount = 0; // Ganti dengan logika perhitungan diskon
                                            $set('discount_amount', $discount);
                                        }),
                                    Forms\Components\TextInput::make('discount_amount')
                                        ->label('Discount Amount')
                                        ->required()
                                        ->default(0)
                                        ->readOnly()
                                        ->numeric(), // Menempati 1 kolom
                                    Forms\Components\TextInput::make('grand_total_amount')
                                        ->required()
                                        ->numeric()
                                        ->columnSpanFull(),
                                ]),
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
                                            // Fetch data Provinsi dari API
                                            $response = file_get_contents('https://matterisnanto.github.io/api-wilayah-indonesia/api/provinces.json');
                                            $provinces = json_decode($response, true);

                                            // Format data untuk options
                                            $options = [];
                                            foreach ($provinces as $province) {
                                                $options[$province['id']] = $province['name'];
                                            }

                                            return $options;
                                        })
                                        ->searchable()
                                        ->reactive()
                                        ->columnSpanFull(),
                                    Forms\Components\Select::make('city_regency')
                                        ->label('City/Regency')
                                        ->required()
                                        ->options(function (callable $get) {
                                            // Ambil province_id yang dipilih
                                            $province = $get('province');

                                            // Jika province_id belum dipilih, kembalikan array kosong
                                            if (!$province) {
                                                return [];
                                            }

                                            // Fetch data Kabupaten/Kota berdasarkan province_id
                                            $response = file_get_contents("https://matterisnanto.github.io/api-wilayah-indonesia/api/regencies/{$province}.json");
                                            $regencies = json_decode($response, true);

                                            // Format data untuk options
                                            $options = [];
                                            foreach ($regencies as $regency) {
                                                $options[$regency['id']] = $regency['name'];
                                            }

                                            return $options;
                                        })
                                        ->searchable()
                                        ->reactive()
                                        ->columnSpanFull(),
                                    Forms\Components\Select::make('district')
                                        ->label('District')
                                        ->required()
                                        ->options(function (callable $get) {
                                            // Ambil regency_id yang dipilih
                                            $regency = $get('city_regency');

                                            // Jika regency_id belum dipilih, kembalikan array kosong
                                            if (!$regency) {
                                                return [];
                                            }

                                            // Fetch data Kecamatan berdasarkan regency_id
                                            $response = file_get_contents("https://matterisnanto.github.io/api-wilayah-indonesia/api/districts/{$regency}.json");
                                            $districts = json_decode($response, true);

                                            // Format data untuk options
                                            $options = [];
                                            foreach ($districts as $district) {
                                                $options[$district['id']] = $district['name'];
                                            }

                                            return $options;
                                        })
                                        ->searchable()
                                        ->reactive()
                                        ->columnSpanFull(),
                                    Forms\Components\Select::make('vilage_subdistrict')
                                        ->label('Village/Subdistrict')
                                        ->required()
                                        ->options(function (callable $get) {
                                            // Ambil district_id yang dipilih
                                            $district = $get('district');

                                            // Jika district_id belum dipilih, kembalikan array kosong
                                            if (!$district) {
                                                return [];
                                            }

                                            // Fetch data Kelurahan/Desa berdasarkan district_id
                                            $response = file_get_contents("https://matterisnanto.github.io/api-wilayah-indonesia/api/villages/{$district}.json");
                                            $villages = json_decode($response, true);

                                            // Format data untuk options
                                            $options = [];
                                            foreach ($villages as $village) {
                                                $options[$village['id']] = $village['name'];
                                            }

                                            return $options;
                                        })
                                        ->searchable()
                                        ->reactive()
                                        ->columnSpanFull(),
                                    Forms\Components\TextInput::make('post_code')
                                        ->label('Post Code')
                                        ->required()
                                        ->numeric()
                                        ->columnSpanFull(),
                                    Forms\Components\TextInput::make('address')
                                        ->label('Address')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('Enter RT/RW, street/alley name, and landmarks')
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),
                        ]),
                    Forms\Components\Wizard\Step::make('Transaction Details')
                        ->description('')
                        ->schema([
                            Forms\Components\TextInput::make('trx_id')
                                ->label('Booking Trx Number')
                                ->required()
                                ->maxLength(255),
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
                                ->required(),
                            Forms\Components\Toggle::make('is_paid')
                                ->label('Already Paid?')
                                ->required(),
                        ]),
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
