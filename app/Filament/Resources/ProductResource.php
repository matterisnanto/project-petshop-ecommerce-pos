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
use Filament\Support\Enums\FontWeight;
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

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationGroup = 'Product Resource';

    protected static ?string $navigationBadgeTooltip = 'Active Product';


    public static function getNavigationBadge(): ?string
    {
        return (string) Product::where('is_active', true)->count();
    }


    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }


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
                //Tables\Columns\ImageColumn::make('thumbnail')
                    //->label('')
                    //->square()
                    //->width(60)
                    //->height(60)
                    //->grow(false),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('PRODUCT NAME')
                    ->alignCenter()
                    ->weight(FontWeight::Bold)
                    ->description(fn ($record) => $record->barcode)
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('BRAND')
                    ->sortable()
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('category.name')
                    ->label('CATEGORY')
                    ->sortable()
                    ->color('gray'),
                
                Tables\Columns\TextColumn::make('selling_price')
                    ->label('PRICE')
                    ->sortable()
                    ->alignEnd()
                    ->money('IDR', locale: 'id')
                    ->color('success')
                    ->weight(FontWeight::Bold),
                
                Tables\Columns\TextColumn::make('stock')
                    ->label('STOCK')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->weight(fn ($state) => $state <= 5 ? 'bold' : null)
                    ->color(fn ($state) => $state <= 5 ? 'danger' : 'success')
                    ->icon(fn ($state) => $state <= 5 ? 'heroicon-o-exclamation-triangle' : null)
                    ->iconPosition('right')
                    ->tooltip(fn ($state) => $state <= 5 
                        ? 'Low stock! Please restock soon' 
                        : 'Stock available'
                    ),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('ACTIVE')
                    ->boolean()
                    ->alignCenter(),
                
                Tables\Columns\IconColumn::make('is_popular')
                    ->label('POPULAR') 
                    ->boolean()
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('ADDED ON')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable(),
                    
                Tables\Filters\SelectFilter::make('brand_id')
                    ->label('Brand')
                    ->relationship('brand', 'name')
                    ->searchable(),
                    
                Tables\Filters\SelectFilter::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected')
                        ->icon('heroicon-o-trash'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No products found')
            ->emptyStateDescription('Create your first product')
            ->striped()
            ->deferLoading();
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