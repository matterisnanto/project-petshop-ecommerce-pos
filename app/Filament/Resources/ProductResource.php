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

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationGroup = 'Product Resource';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Information')
                    ->description('Basic product details')
                    ->icon('heroicon-o-shopping-bag')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Product Name')
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('slug', Product::generateUniqueSlug($state));
                                    })
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255)
                                    ->placeholder('Enter product name')
                                    ->columnSpan(['md' => 2]),

                                Forms\Components\TextInput::make('slug')
                                    ->label('URL Identifier')
                                    ->required()
                                    ->readOnly()
                                    ->maxLength(255)
                                    ->helperText('Auto-generated from product name')
                                    ->columnSpan(['md' => 2]),

                                Forms\Components\TextInput::make('barcode')
                                    ->maxLength(255)
                                    ->placeholder('Enter barcode')
                                    ->prefixIcon('lucide-barcode')
                                    ->columnSpan(['md' => 2]),

                                Forms\Components\TextInput::make('stock')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefixIcon('heroicon-o-archive-box')
                                    ->default(1)
                                    ->columnSpan(['md' => 1]),

                                Forms\Components\TextInput::make('weight')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->suffix('grams')
                                    ->columnSpan(['md' => 1]),
                            ])
                            ->columns(2),

                        Forms\Components\Textarea::make('description')
                            ->label('Product Description')
                            ->columnSpanFull()
                            ->rows(4)
                            ->placeholder('Detailed product description...'),

                        Forms\Components\FileUpload::make('thumbnail')
                            ->label('Main Product Image')
                            ->image()
                            ->required()
                            ->directory('product-thumbnails')
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->panelAspectRatio('2:1')
                            ->columnSpanFull()
                            ->helperText('Recommended size: 800x800px'),

                        Forms\Components\Repeater::make('photos')
                            ->label('Additional Product Images')
                            ->relationship('photos')
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->image()
                                    ->directory('product-gallery')
                                    ->imageEditor()
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('1:1')
                                    ->required(),
                            ])
                            ->grid(2)
                            ->defaultItems(1)
                            ->createItemButtonLabel('Add another image')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Pricing')
                    ->description('Product pricing information')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('purchase_price')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('Rp')
                                    ->columnSpan(['md' => 1]),

                                Forms\Components\TextInput::make('selling_price')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('Rp')
                                    ->columnSpan(['md' => 1]),
                            ])
                            ->columns(2),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Classification')
                    ->description('Product categorization')
                    ->icon('heroicon-o-tag')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->label('Product Category')
                                    ->prefixIcon('heroicon-o-circle-stack')
                                    ->native(false)
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\Section::make('New Product Category')
                                            ->icon('heroicon-o-circle-stack')
                                            ->schema([
                                                Forms\Components\Grid::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('name')
                                                            ->label('Category Name')
                                                            ->afterStateUpdated(function (Set $set, $state) {
                                                                $set('slug', Category::generateUniqueSlug($state));
                                                            })
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->maxLength(255)
                                                            ->placeholder('e.g., Cat Food, Dog Food, Toys')
                                                            ->helperText('The display name for your category')
                                                            ->columnSpan(['md' => 2]),

                                                        Forms\Components\TextInput::make('slug')
                                                            ->label('URL Identifier')
                                                            ->required()
                                                            ->readOnly()
                                                            ->maxLength(255)
                                                            ->helperText('Auto-generated from category name')
                                                            ->columnSpan(['md' => 2]),
                                                    ])
                                                    ->columns(2),

                                                Forms\Components\FileUpload::make('icon')
                                                    ->label('Category Icon')
                                                    ->image()
                                                    ->directory('category-icons')
                                                    ->imageEditor()
                                                    ->imageResizeMode('contain')
                                                    ->imageCropAspectRatio('1:1')
                                                    ->imagePreviewHeight('150')
                                                    ->maxSize(512)
                                                    ->helperText('Upload a square icon (recommended 200x200px)')
                                                    ->downloadable()
                                                    ->columnSpanFull()
                                                    ->panelAspectRatio('2:1'),
                                            ])
                                            ->columns(2)
                                            ->collapsible(),
                                    ])
                                    ->columnSpan(['md' => 1])
                                    ->helperText('Select or create specific product category'),

                                Forms\Components\Select::make('brand_id')
                                    ->relationship('brand', 'name')
                                    ->label('Product Brand')
                                    ->prefixIcon('heroicon-o-tag')
                                    ->native(false)
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([

                                        Forms\Components\Section::make('New Product Brand')
                                            ->icon('heroicon-o-tag')
                                            ->schema([
                                                Forms\Components\Grid::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('name')
                                                            ->label('Brand Name')
                                                            ->afterStateUpdated(function (Set $set, $state) {
                                                                $set('slug', Brand::generateUniqueSlug($state));
                                                            })
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->maxLength(255)
                                                            ->placeholder('e.g., Whiskas, Purina')
                                                            ->helperText('The official name of your brand')
                                                            ->columnSpan(['md' => 2]),

                                                        Forms\Components\TextInput::make('slug')
                                                            ->label('URL Slug')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->helperText('Will be auto-generated from name')
                                                            ->columnSpan(['md' => 2]),
                                                    ])
                                                    ->columns(2),

                                                Forms\Components\FileUpload::make('logo')
                                                    ->label('Brand Logo')
                                                    ->image()
                                                    ->directory('brand-logos')
                                                    ->imageEditor()
                                                    ->imageResizeMode('contain')
                                                    ->imageCropAspectRatio('1:1')
                                                    ->imagePreviewHeight('150')
                                                    ->maxSize(1024)
                                                    ->required()
                                                    ->helperText('Upload a square logo (max 1MB)')
                                                    ->columnSpanFull()
                                                    ->panelAspectRatio('2:1'),
                                            ])
                                            ->columns(2)
                                            ->collapsible(),

                                    ])
                                    ->columnSpan(['md' => 1])
                                    ->helperText('Select or create specific product brand'),
                            ])
                            ->columns(2),

                        Forms\Components\Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->label('Supplier')
                            ->prefixIcon('heroicon-o-truck')
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\Section::make('New Supplier')
                                    ->icon('heroicon-o-user-circle')
                                    ->schema([
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Supplier Name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('e.g., PT. Supplier Maju Jaya')
                                                    ->columnSpan(['md' => 2]),

                                                Forms\Components\TextInput::make('email')
                                                    ->label('Email Address')
                                                    ->email()
                                                    ->unique()
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('supplier@example.com')
                                                    ->prefixIcon('heroicon-o-envelope')
                                                    ->columnSpan(['md' => 2]),
                                            ])
                                            ->columns(2),

                                        Forms\Components\TextInput::make('phone')
                                            ->label('Phone Number')
                                            ->tel()
                                            ->required()
                                            ->maxLength(255)
                                            ->mask('999999999999')
                                            ->prefix('+62')
                                            ->stripCharacters(['-', ' '])
                                            ->rule('digits_between:10,13')
                                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                                                // Remove +62 if already present to avoid duplication
                                                $cleaned = str_replace('+62', '', $state);
                                                $component->state($cleaned);
                                            })
                                            ->dehydrateStateUsing(fn($state) => '+62' . $state)
                                            ->placeholder('81234567890')
                                            ->prefixIcon('heroicon-o-phone')
                                            ->helperText('Enter number without +62 (e.g., 81234567890)')
                                            ->columnSpan(['md' => 2]),

                                        Forms\Components\Textarea::make('address')
                                            ->label('Full Address')
                                            ->required()
                                            ->maxLength(255)
                                            ->rows(3)
                                            ->placeholder('Street, City, Province, Postal Code')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->collapsible(),
                            ])
                            ->columnSpanFull()
                            ->helperText('Select or create supplier'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Status')
                    ->description('Product visibility settings')
                    ->icon('heroicon-o-eye')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active Product')
                            ->onColor('success')
                            ->offColor('danger')
                            ->inline(false)
                            ->columnSpan(['md' => 1]),

                        Forms\Components\Toggle::make('is_popular')
                            ->label('Popular Product')
                            ->onColor('success')
                            ->offColor('danger')
                            ->inline(false)
                            ->columnSpan(['md' => 1]),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Product Photo')
                    ->square(),
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
            // 'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
