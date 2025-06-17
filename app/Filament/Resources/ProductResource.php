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
use App\Models\CategoryAnimals;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Storage;
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
                    ->description('Enter basic details about your product')
                    ->icon('heroicon-o-shopping-bag')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Product Name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255)
                                    ->placeholder('e.g., Premium Dog Food 5kg')
                                    ->columnSpan(['md' => 2])
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        if (!empty($state)) {
                                            $set('slug', Product::generateUniqueSlug($state));
                                        }
                                    }),

                                Forms\Components\TextInput::make('slug')
                                    ->label('URL Slug')
                                    ->required()
                                    ->readOnly()
                                    ->maxLength(255)
                                    ->columnSpan(['md' => 2])
                                    ->helperText('Automatically generated from product name'),

                                Forms\Components\TextInput::make('barcode')
                                    ->label('Barcode/SKU')
                                    ->maxLength(255)
                                    ->placeholder('e.g., 123456789012')
                                    ->prefixIcon('lucide-barcode')
                                    ->columnSpan(['md' => 2]),

                                Forms\Components\TextInput::make('stock')
                                    ->label('Inventory Quantity')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefixIcon('heroicon-o-archive-box')
                                    ->default(1)
                                    ->columnSpan(['md' => 1]),

                                Forms\Components\TextInput::make('weight')
                                    ->label('Product Weight')
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
                            ->placeholder('Describe the product features, benefits, and specifications...')
                            ->helperText('This will be displayed on the product page'),

                        Forms\Components\FileUpload::make('thumbnail')
                            ->label('Main Product Image')
                            ->image()
                            ->required()
                            ->directory('product-thumbnails')
                            ->imageEditor()
                            ->downloadable()
                            ->openable()
                            ->panelLayout('integrated')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->deleteUploadedFileUsing(function ($state, $livewire, $record) {

                                if ($record?->thumbnail) {
                                    Storage::disk('public')->delete($record->thumbnail);
                                }
                                return true;
                            })
                            ->columnSpanFull()
                            ->helperText('Upload a high-quality square image (800×800px recommended)')
                            ->hint('Click to upload or drag & drop')
                            ->hintIcon('heroicon-o-information-circle'),

                        Forms\Components\Repeater::make('photos')
                            ->label('Additional Images')
                            ->relationship('photos')
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->label('Image')
                                    ->image()
                                    ->directory('product-gallery')
                                    ->imageEditor()
                                    ->downloadable()
                                    ->openable()
                                    ->panelLayout('integrated')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('1:1')
                                    ->deleteUploadedFileUsing(function ($state, $livewire, $record) {

                                        if ($record?->photo) {
                                            Storage::disk('public')->delete($record->photo);
                                        }
                                        return true;
                                    })
                                    ->required()
                                    ->helperText('Additional product views or angles'),
                            ])
                            ->grid(2)
                            ->defaultItems(1)
                            ->createItemButtonLabel('+ Add Image')
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Pricing')
                    ->description('Set your product pricing')
                    ->icon('heroicon-o-currency-dollar')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('purchase_price')
                                    ->label('Purchase Price')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('Rp')
                                    ->columnSpan(['md' => 1]),

                                Forms\Components\TextInput::make('selling_price')
                                    ->label('Selling Price')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('Rp')
                                    ->columnSpan(['md' => 1]),
                            ])
                            ->columns(2)
                            ->extraAttributes(['class' => 'bg-gray-50 dark:bg-gray-800 p-4 rounded-lg']),
                    ]),

                Forms\Components\Section::make('Categories & Branding')
                    ->description('Organize your product')
                    ->icon('heroicon-o-tag')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->label('Product Category')
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                // Keep existing create option form exactly as is
                            ])
                            ->columnSpan(['md' => 1])
                            ->helperText('Main product category')
                            ->loadingMessage('Loading categories...')
                            ->noSearchResultsMessage('No categories found')
                            ->searchPrompt('Search categories'),

                        Forms\Components\Select::make('brand_id')
                            ->relationship('brand', 'name')
                            ->label('Brand')
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                // Keep existing create option form exactly as is
                            ])
                            ->columnSpan(['md' => 1])
                            ->helperText('Product manufacturer/brand'),

                        Forms\Components\Select::make('category_animals_id')
                            ->relationship('categoryAnimals', 'name')
                            ->label('Animal Type')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->createOptionForm([
                                // Keep existing create option form exactly as is
                            ])
                            ->columnSpan(['md' => 2])
                            ->helperText('Which animals is this product for?'),
                    ]),

                Forms\Components\Section::make('Visibility & Status')
                    ->description('Control product visibility')
                    ->icon('heroicon-o-eye')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active Product')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->inline(false)
                                    ->helperText('Visible to customers when active')
                                    ->columnSpan(['md' => 1]),

                                Forms\Components\Toggle::make('is_popular')
                                    ->label('Mark as Popular')
                                    ->onColor('warning')
                                    ->offColor('gray')
                                    ->inline(false)
                                    ->helperText('Featured in popular sections')
                                    ->columnSpan(['md' => 1]),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('')
                    ->square()
                    ->width(60)
                    ->height(60)
                    ->grow(false)
                    ->toggleable()
                    ->extraImgAttributes(['class' => 'rounded-lg border border-gray-200']),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Product Name')
                    ->weight(FontWeight::Bold)
                    ->description(fn($record) => $record->barcode ? "SKU: {$record->barcode}" : null)
                    ->wrap()
                    ->tooltip(fn($record) => $record->description)
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Brand')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-tag')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-circle-stack')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('categoryAnimals.name')
                    ->label('Animal')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->icon('lucide-paw-print')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('selling_price')
                    ->label('Price')
                    ->sortable()
                    ->money('IDR', locale: 'id')
                    ->color('success')
                    ->weight(FontWeight::Bold)
                    ->description(fn($record) => 'Margin: Rp' . number_format($record->selling_price - $record->purchase_price, 0, ',', '.'))
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->color(fn($state) => $state <= 5 ? 'danger' : ($state <= 15 ? 'warning' : 'success'))
                    ->weight(FontWeight::Bold)
                    ->icon(fn($state) => match (true) {
                        $state <= 5 => 'heroicon-o-exclamation-triangle',
                        $state <= 15 => 'heroicon-o-arrow-trending-down',
                        default => 'heroicon-o-check-badge'
                    })
                    ->tooltip(fn($state) => match (true) {
                        $state <= 5 => 'Critical stock! Reorder now',
                        $state <= 15 => 'Low stock - consider reordering',
                        default => 'Stock available'
                    })
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->trueColor('success')
                    ->falseIcon('heroicon-o-x-circle')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_popular')
                    ->label('Popular')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseIcon('heroicon-o-star')
                    ->falseColor('gray')
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\SelectFilter::make('brand_id')
                    ->label('Brand')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\SelectFilter::make('category_animals_id')
                    ->label('Animal Category')
                    ->relationship('categoryAnimals', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->trueLabel('Active products')
                    ->falseLabel('Inactive products')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_popular')
                    ->label('Popular Status')
                    ->trueLabel('Popular products')
                    ->falseLabel('Non-popular products')
                    ->native(false),

                Tables\Filters\Filter::make('low_stock')
                    ->label('Low Stock Alert')
                    ->query(fn(Builder $query) => $query->where('stock', '<=', 5))
                    ->toggle(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    ->color('primary')
                    ->tooltip('View Details'),

                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->color('success')
                    ->tooltip('Edit Product'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->tooltip('Delete Product'),

                Tables\Actions\RestoreAction::make()
                    ->iconButton()
                    ->color('warning')
                    ->tooltip('Restore Product'),
                Tables\Actions\ForceDeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->tooltip('Force Delete Products')
                    ->before(function (Product $record) {
                        // Delete thumbnail
                        if ($record->thumbnail) {
                            Storage::disk('public')->delete($record->thumbnail);
                        }

                        // Delete all product photos
                        $record->photos->each(function ($photo) {
                            if ($photo->photo) {
                                Storage::disk('public')->delete($photo->photo);
                            }
                        });
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation(),

                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Restore Selected')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->requiresConfirmation(),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('Permanently Delete Selected')
                        ->icon('heroicon-o-trash')
                        ->before(function ($records) {
                            $records->each(function ($record) {
                                if ($record->thumbnail) {
                                    Storage::disk('public')->delete($record->thumbnail);
                                }
                                $record->photos->each(function ($photo) {
                                    if ($photo->photo) {
                                        Storage::disk('public')->delete($photo->photo);
                                    }
                                });
                            });
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No products found')
            ->emptyStateDescription('Click "Create product" to add your first product')
            ->emptyStateIcon('heroicon-o-shopping-bag')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create product')
                    ->icon('heroicon-o-plus'),
            ])
            ->striped()
            ->deferLoading()
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->groups([
                Tables\Grouping\Group::make('category.name')
                    ->label('Category')
                    ->collapsible(),

                Tables\Grouping\Group::make('brand.name')
                    ->label('Brand')
                    ->collapsible(),
                Tables\Grouping\Group::make('categoryAnimals.name')
                    ->label('Category Animal')
                    ->collapsible(),
            ])
            ->groupingSettingsInDropdownOnDesktop()
            ->defaultGroup('category.name');
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
