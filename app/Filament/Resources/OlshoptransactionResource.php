<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OlshoptransactionResource\Pages;
use App\Filament\Resources\OlshoptransactionResource\RelationManagers;
use App\Models\Olshoptransaction;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                    Forms\Components\Wizard\Step::make('Product and price')
                        ->description('')
                        ->schema([
                            Forms\Components\Grid::make()
                                ->schema([
                                    Forms\Components\Select::make('product_id')
                                        ->relationship('products', 'name')
                                        ->required()
                                        ->searchable()
                                        ->live()
                                        ->preload()
                                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                            $product = Product::find($state);
                                            $price = $product ? $product->selling_price : 0;
                                            $quantity = $get('quantity') ?? 1;
                                            $subTotalAmount = $price * $quantity;
                                            $discount = $get('discount_amount') ?? 0;
                                            $grandTotalAmount = $subTotalAmount - $discount;

                                            $set('quantity', $quantity);
                                            $set('price', $price);
                                            $set('sub_total_amount', $subTotalAmount);
                                            $set('grand_total_amount', $grandTotalAmount);
                                        }),
                                    Forms\Components\TextInput::make('quantity')
                                        ->required()
                                        ->numeric()
                                        ->prefix('Qty')
                                        ->live()
                                        // ->default(1)
                                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                            $price = $get('price') ?? 0;
                                            $quantity = $state;
                                            $subTotalAmount = $price * $quantity;

                                            $set('sub_total_amount', $subTotalAmount);

                                            $discount = $get('discount_amount') ?? 0;
                                            $grandTotalAmount = $subTotalAmount - $discount;
                                            $set('grand_total_amount', $grandTotalAmount);
                                        }),
                                    Forms\Components\Select::make('promo_code_id')
                                        ->label('Promo Code')
                                        ->relationship('promocode', 'code')
                                        ->default(null),
                                    Forms\Components\TextInput::make('discount_amount')
                                        ->required()
                                        ->default(0)
                                        ->readOnly()
                                        ->numeric(),
                                    Forms\Components\TextInput::make('sub_total_amount')
                                        ->required()
                                        ->numeric(),
                                    Forms\Components\TextInput::make('grand_total_amount')
                                        ->required()
                                        ->readOnly()
                                        ->numeric(),
                                ]),
                        ]),
                    Forms\Components\Wizard\Step::make('Customer Information')
                        ->description('')
                        ->schema([
                            Forms\Components\Grid::make()
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpanFull(),
                                    Forms\Components\TextInput::make('phone')
                                        ->tel()
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('email')
                                        ->email()
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\Select::make('province')
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
                                        ->required()
                                        ->numeric(),
                                    Forms\Components\TextInput::make('address')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('Enter RT/RW, street/alley name, and landmarks'),
                                ]),

                        ]),
                    Forms\Components\Wizard\Step::make('Transaction Details')
                        ->description('')
                        ->schema([
                            Forms\Components\TextInput::make('booking_trx')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\FileUpload::make('proof')
                                ->image()
                                ->required(),
                            Forms\Components\Toggle::make('is_paid')
                                ->required(),
                        ]),
                ])
                    ->columnSpan('full')
                    ->columns(1)
                    ->skippable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('promo_code_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sub_total_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('grand_total_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('post_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_paid')
                    ->boolean(),
                Tables\Columns\TextColumn::make('booking_trx')
                    ->searchable(),
                Tables\Columns\TextColumn::make('proof')
                    ->searchable(),
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'edit' => Pages\EditOlshoptransaction::route('/{record}/edit'),
        ];
    }
}
