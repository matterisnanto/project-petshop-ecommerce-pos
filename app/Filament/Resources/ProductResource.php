<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Brand;
use App\Models\Product;
use Filament\Forms\Set;
use App\Models\Category;
use App\Models\Supplier;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Product';

    protected static ?string $modelLabel = 'Product';

    protected static ?string $pluralModelLabel = 'Product';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationGroup = 'Product Resource';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('slug', Product::generateUniqueSlug($state));
                            })
                            ->required()
                            ->live(onBlur: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('barcode')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('stock')
                            ->required()
                            ->numeric()
                            ->default(1),
                        Forms\Components\Textarea::make('about')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('thumbnail')
                            ->image()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Repeater::make('photos')
                            ->relationship('photos')
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->required(),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('purchase_price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('selling_price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Additional')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->label('Category')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('slug', Category::generateUniqueSlug($state));
                                    })
                                    ->required()
                                    ->live(onBlur: true)
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->readOnly()
                                    ->maxLength(255),
                                Forms\Components\FileUpload::make('icon')
                                    ->image()
                                    ->columnSpan('full')
                                    ->default(null),
                            ])
                            ->default(null),
                        Forms\Components\Select::make('brand_id')
                            ->relationship('brand', 'name')
                            ->label('Brand')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('slug', Brand::generateUniqueSlug($state));
                                    })
                                    ->required()
                                    ->live(onBlur: true)
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\FileUpload::make('logo')
                                    ->image()
                                    ->columnSpan('full')
                                    ->required(),
                            ])
                            ->default(null),
                        Forms\Components\Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->label('Supplier Name')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Supplier Name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->unique()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->mask('999-9999-9999') // Format input
                                    ->prefix('+62') // Tambahkan prefix
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('address')
                                    ->label('Address')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->default(null)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),


                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->required(),
                        Forms\Components\Toggle::make('is_popular')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('barcode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('selling_price')
                    ->label('selling price')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        // Format nilai sebagai Rupiah
                        return 'Rp ' . number_format($state, 0, ',', '.');
                    }),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\IconColumn::make('is_popular')
                    ->boolean()
                    ->label('Popular'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('category')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('brand_id')
                    ->label('brand')
                    ->relationship('brand', 'name'),
                Tables\Filters\SelectFilter::make('supplier_id')
                    ->label('supplier')
                    ->relationship('supplier', 'name'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
