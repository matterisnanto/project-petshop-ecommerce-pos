<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Breeds;
use App\Models\Animals;
use App\Models\Product;
use Filament\Forms\Set;
use App\Models\Category;
use App\Models\Supplier;
use Filament\Forms\Form;
use App\Models\Purchases;
use Filament\Tables\Table;
use App\Models\CategoryAnimals;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PurchasesResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PurchasesResource\RelationManagers;

class PurchasesResource extends Resource
{
    protected static ?string $model = Purchases::class;

    protected static ?string $navigationLabel = 'Purchasing';

    protected static ?string $modelLabel = 'Purchasing';

    protected static ?string $pluralModelLabel = 'Purchasing';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationGroup = 'Purchasing Resource';

    public static function getNavigationIcon(): string
    {
        return 'lucide-package-plus';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // SECTION 1: GENERAL PURCHASE INFORMATION
                Forms\Components\Section::make('General Purchase Information')
                    ->icon('heroicon-o-shopping-bag')
                    ->description('Basic information about this purchase')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('purchase_number')
                                    ->label('Purchase Number')
                                    ->required()
                                    ->columnSpan(['md' => 1])
                                    ->prefixIcon('heroicon-o-hashtag'),

                                Forms\Components\DatePicker::make('purchase_date')
                                    ->label('Purchase Date')
                                    ->default(now())
                                    ->required()
                                    ->columnSpan(['md' => 1])
                                    ->prefixIcon('heroicon-o-calendar'),

                                Forms\Components\Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'ordered' => 'Ordered',
                                        'received' => 'Received',
                                        'cancelled' => 'Cancelled'
                                    ])
                                    ->default('draft')
                                    ->required()
                                    ->columnSpan(['md' => 1]),
                            ])
                            ->columns(3),

                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Select::make('supplier_id')
                                    ->label('Supplier')
                                    ->relationship('supplier', 'name')
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\Section::make('New Supplier Information')
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
                                                        $cleaned = str_replace('+62', '', $state);
                                                        $component->state($cleaned);
                                                    })
                                                    ->dehydrateStateUsing(fn($state) => '+62' . $state)
                                                    ->placeholder('81234567890')
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
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(['md' => 2]),

                                Forms\Components\FileUpload::make('proof')
                                    ->label('Proof of Purchase')
                                    ->directory('purchase-proofs')
                                    ->image()
                                    ->columnSpan(['md' => 1])
                                    ->helperText('Upload invoice or receipt'),
                            ])
                            ->columns(3),
                    ])
                    ->columns(2)
                    ->collapsible(),

                // SECTION 2: PURCHASE ITEMS
                Forms\Components\Section::make('Purchase Items')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->description('Add products or animals to this purchase')
                    ->schema([
                        Forms\Components\Repeater::make('orders')
                            ->relationship()
                            ->label('Items')
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\Select::make('type')
                                            ->label('Item Type')
                                            ->options([
                                                'product' => 'Product',
                                                'animal' => 'Animal'
                                            ])
                                            ->required()
                                            ->reactive()
                                            ->native(false)
                                            ->columnSpanFull(),
                                        Forms\Components\Select::make('product_id')
                                            ->label('Select Product')
                                            ->options(Product::query()->pluck('name', 'id'))
                                            ->searchable()
                                            ->live()
                                            ->hidden(fn($get) => $get('type') !== 'product')
                                            ->columnSpan(['md' => 2])
                                            ->createOptionForm([
                                                Forms\Components\Section::make('Product Information')
                                                    ->description('Basic product details')
                                                    ->icon('heroicon-o-shopping-bag')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('name')
                                                            ->label('Product Name')
                                                            ->afterStateUpdated(function (Set $set, $state) {
                                                                if (!empty($state)) {
                                                                    $set('slug', Product::generateUniqueSlug($state));
                                                                }
                                                            })
                                                            ->live(onBlur: true)
                                                            ->maxLength(255)
                                                            ->placeholder('Enter product name')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('slug')
                                                            ->label('URL Identifier')
                                                            ->required()
                                                            ->readOnly()
                                                            ->maxLength(255)
                                                            ->helperText('Auto-generated from product name')
                                                            ->required(),
                                                        Forms\Components\Grid::make()
                                                            ->schema([
                                                                Forms\Components\TextInput::make('barcode')
                                                                    ->label('Barcode')
                                                                    ->maxLength(255)
                                                                    ->placeholder('Enter barcode')
                                                                    ->prefixIcon('lucide-barcode')
                                                                    ->required(),
                                                                Forms\Components\TextInput::make('weight')
                                                                    ->label('Weight')
                                                                    ->required()
                                                                    ->numeric()
                                                                    ->minValue(0)
                                                                    ->suffix('grams'),

                                                            ]),
                                                        Forms\Components\FileUpload::make('thumbnail')
                                                            ->label('Main Product Image')
                                                            ->image()
                                                            ->required()
                                                            ->directory('product-thumbnails')
                                                            ->imageEditor()
                                                            ->imageResizeMode('cover')
                                                            ->imageCropAspectRatio('1:1')
                                                            ->helperText('Recommended size: 800x800px'),
                                                        Forms\Components\Textarea::make('description')
                                                            ->label('Product Description')
                                                            ->columnSpanFull()
                                                            ->rows(4)
                                                            ->placeholder('Detailed product description...'),
                                                    ]),
                                                Forms\Components\Section::make('Classification')
                                                    ->description('Product categorization')
                                                    ->icon('heroicon-o-tag')
                                                    ->schema([
                                                        Forms\Components\Select::make('category_id')
                                                            ->label('Category')
                                                            ->options(Category::all()->pluck('name', 'id'))
                                                            ->searchable()
                                                            ->createOptionForm([
                                                                Forms\Components\Section::make('New Product Category')
                                                                    ->icon('heroicon-o-circle-stack')
                                                                    ->schema([
                                                                        Forms\Components\Grid::make()
                                                                            ->schema([
                                                                                Forms\Components\TextInput::make('name')
                                                                                    ->label('Category Name')
                                                                                    ->afterStateUpdated(function (Set $set, $state) {
                                                                                        if (!empty($state)) {
                                                                                            $set('slug', Category::generateUniqueSlug($state));
                                                                                        }
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
                                                            ->helperText('Select or create specific product category')
                                                            ->createOptionUsing(function (array $data) {
                                                                return Category::create($data)->id;
                                                            })
                                                            ->columnSpan(['md' => 1]),
                                                        Forms\Components\Select::make('brand_id')
                                                            ->label('Brand')
                                                            ->options(Brand::all()->pluck('name', 'id'))
                                                            ->searchable()
                                                            ->createOptionForm([
                                                                Forms\Components\Section::make('New Product Brand')
                                                                    ->icon('heroicon-o-tag')
                                                                    ->schema([
                                                                        Forms\Components\Grid::make()
                                                                            ->schema([
                                                                                Forms\Components\TextInput::make('name')
                                                                                    ->label('Brand Name')
                                                                                    ->afterStateUpdated(function (Set $set, $state) {
                                                                                        if (!empty($state)) {
                                                                                            $set('slug', Brand::generateUniqueSlug($state));
                                                                                        }
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
                                                            ->helperText('Select or create specific product brand')
                                                            ->createOptionUsing(function (array $data) {
                                                                return Brand::create($data)->id;
                                                            })
                                                            ->columnSpan(['md' => 1]),
                                                    ])->columns(2),
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
                                            ])
                                            ->createOptionUsing(function (array $data) {
                                                return Product::create($data)->id;
                                            }),
                                        Forms\Components\Select::make('animals_id')
                                            ->label('Select Animal')
                                            ->options(Animals::query()->pluck('name', 'id'))
                                            ->searchable()
                                            ->live()
                                            ->hidden(fn($get) => $get('type') !== 'animal')
                                            ->columnSpan(['md' => 2])
                                            ->createOptionForm([
                                                Forms\Components\Section::make('Basic Information')
                                                    ->description('Primary details about the animal')
                                                    ->icon('heroicon-o-identification')
                                                    ->schema([
                                                        Forms\Components\Grid::make()
                                                            ->schema([
                                                                Forms\Components\TextInput::make('name')
                                                                    ->label('Name')
                                                                    ->afterStateUpdated(function (Set $set, $state) {
                                                                        if (!empty($state)) {
                                                                            $set('slug', Animals::generateUniqueSlug($state));
                                                                        }
                                                                    })
                                                                    ->required()
                                                                    ->live(onBlur: true)
                                                                    ->columnSpan(['md' => 2])
                                                                    ->maxLength(255)
                                                                    ->placeholder('Animal name')
                                                                    ->hint('The official name of the animal'),
                                                                Forms\Components\Hidden::make('slug'),
                                                                Forms\Components\TextInput::make('barcode')
                                                                    ->label('Barcode')
                                                                    ->maxLength(255)
                                                                    ->columnSpan(['md' => 1])
                                                                    ->prefixIcon('lucide-barcode'),
                                                            ])->columns(3),
                                                        Forms\Components\Grid::make()
                                                            ->schema([
                                                                Forms\Components\Select::make('category_id')
                                                                    ->label('Category')
                                                                    ->options(CategoryAnimals::all()->pluck('name', 'id'))
                                                                    ->required()
                                                                    ->label('Animal Category')
                                                                    ->searchable()
                                                                    ->preload()
                                                                    ->native(false)
                                                                    ->prefixIcon('heroicon-o-circle-stack')
                                                                    ->placeholder('Select or create a category')
                                                                    ->createOptionForm([
                                                                        Forms\Components\Section::make('New Category Animals')
                                                                            ->icon('heroicon-o-circle-stack') // Added icon
                                                                            ->collapsible() // Make section collapsible
                                                                            ->schema([
                                                                                Forms\Components\Grid::make()
                                                                                    ->schema([
                                                                                        Forms\Components\TextInput::make('name')
                                                                                            ->label('Category Name')
                                                                                            ->afterStateUpdated(function (Set $set, $state) {
                                                                                                if (!empty($state)) {
                                                                                                    $set('slug', CategoryAnimals::generateUniqueSlug($state));
                                                                                                }
                                                                                            })
                                                                                            ->required()
                                                                                            ->live(onBlur: true)
                                                                                            ->maxLength(255)
                                                                                            ->placeholder('e.g., Dogs, Cats, Birds')
                                                                                            ->helperText('The display name for this animal category')
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

                                                                                Forms\Components\Textarea::make('description')
                                                                                    ->label('Description')
                                                                                    ->maxLength(255)
                                                                                    ->rows(3)
                                                                                    ->placeholder('Brief description about this animal category')
                                                                                    ->helperText('Max 255 characters')
                                                                                    ->columnSpanFull(),

                                                                                Forms\Components\FileUpload::make('icon')
                                                                                    ->label('Category Icon')
                                                                                    ->image()
                                                                                    ->directory('category-icons')
                                                                                    ->imageEditor()
                                                                                    ->imageResizeMode('contain')
                                                                                    ->imageCropAspectRatio('1:1')
                                                                                    ->panelAspectRatio('2:1')
                                                                                    ->maxSize(512)
                                                                                    ->helperText('Recommended size: 200x200px, max 512KB')
                                                                                    ->columnSpanFull(),
                                                                            ])
                                                                    ])
                                                                    ->createOptionUsing(function (array $data) {
                                                                        return CategoryAnimals::create($data)->id;
                                                                    })
                                                                    ->helperText('Select or create animal category'),
                                                                Forms\Components\Select::make('breeds_id')
                                                                    ->options(Breeds::all()->pluck('name', 'id'))
                                                                    ->required()
                                                                    ->label('Animal Breed')
                                                                    ->native(false)
                                                                    ->searchable()
                                                                    ->preload()
                                                                    ->prefixIcon('lucide-dna')
                                                                    ->createOptionForm([
                                                                        Forms\Components\Grid::make()
                                                                            ->schema([
                                                                                Forms\Components\Section::make('New Breed')
                                                                                    ->icon('lucide-dna')  // More vibrant icon
                                                                                    ->collapsible()  // Allows section to be collapsed
                                                                                    ->columns(2)
                                                                                    ->schema([
                                                                                        // Animal Category Select with enhanced UI
                                                                                        Forms\Components\Select::make('category_animals_id')
                                                                                            ->options(CategoryAnimals::all()->pluck('name', 'id'))
                                                                                            ->label('Animal Category')
                                                                                            ->searchable()
                                                                                            ->preload()
                                                                                            ->native(false)
                                                                                            ->placeholder('Select or create a category')
                                                                                            ->createOptionForm([
                                                                                                Forms\Components\Section::make('New Category Animals')
                                                                                                    ->icon('heroicon-o-circle-stack') // Added icon
                                                                                                    ->collapsible() // Make section collapsible
                                                                                                    ->schema([
                                                                                                        Forms\Components\Grid::make()
                                                                                                            ->schema([
                                                                                                                Forms\Components\TextInput::make('name')
                                                                                                                    ->label('Category Name')
                                                                                                                    ->afterStateUpdated(function (Set $set, $state) {
                                                                                                                        if (!empty($state)) {
                                                                                                                            $set('slug', CategoryAnimals::generateUniqueSlug($state));
                                                                                                                        }
                                                                                                                    })
                                                                                                                    ->required()
                                                                                                                    ->live(onBlur: true)
                                                                                                                    ->maxLength(255)
                                                                                                                    ->placeholder('e.g., Dogs, Cats, Birds')
                                                                                                                    ->helperText('The display name for this animal category')
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

                                                                                                        Forms\Components\Textarea::make('description')
                                                                                                            ->label('Description')
                                                                                                            ->maxLength(255)
                                                                                                            ->rows(3)
                                                                                                            ->placeholder('Brief description about this animal category')
                                                                                                            ->helperText('Max 255 characters')
                                                                                                            ->columnSpanFull(),

                                                                                                        Forms\Components\FileUpload::make('icon')
                                                                                                            ->label('Category Icon')
                                                                                                            ->image()
                                                                                                            ->directory('category-icons')
                                                                                                            ->imageEditor()
                                                                                                            ->imageResizeMode('contain')
                                                                                                            ->imageCropAspectRatio('1:1')
                                                                                                            ->panelAspectRatio('2:1')
                                                                                                            ->maxSize(512)
                                                                                                            ->helperText('Recommended size: 200x200px, max 512KB')
                                                                                                            ->columnSpanFull(),
                                                                                                    ])
                                                                                            ])
                                                                                            ->createOptionUsing(function (array $data) {
                                                                                                return CategoryAnimals::create($data)->id;
                                                                                            })
                                                                                            ->helperText('Select or create animal category')
                                                                                            ->columnSpanFull(),

                                                                                        // Breed Name with visual feedback
                                                                                        Forms\Components\TextInput::make('name')
                                                                                            ->label('Breed Name')
                                                                                            ->afterStateUpdated(function (Set $set, $state) {
                                                                                                if (!empty($state)) {
                                                                                                    $set('slug', Breeds::generateUniqueSlug($state));
                                                                                                }
                                                                                            })
                                                                                            ->required()
                                                                                            ->live(onBlur: true)
                                                                                            ->maxLength(255)
                                                                                            ->columnSpanFull()
                                                                                            ->prefixIcon('lucide-dna'),

                                                                                        // Slug field with copy button
                                                                                        Forms\Components\TextInput::make('slug')
                                                                                            ->label('URL Identifier')
                                                                                            ->required()
                                                                                            ->readOnly()
                                                                                            ->maxLength(255)
                                                                                            ->helperText('Auto-generated from  name')
                                                                                            ->columnSpan(['md' => 2]),
                                                                                    ])
                                                                            ])
                                                                    ])
                                                                    ->createOptionUsing(function (array $data) {
                                                                        return Breeds::create($data)->id;
                                                                    }),
                                                            ])->columns(2),

                                                        Forms\Components\FileUpload::make('thumbnail')
                                                            ->label('Main Photo')
                                                            ->image()
                                                            ->required()
                                                            ->directory('animal-thumbnails')
                                                            ->imageEditor()
                                                            ->imageResizeMode('cover')
                                                            ->imageCropAspectRatio('1:1')
                                                            ->columnSpanFull()
                                                            ->hint('Primary display image (1:1 ratio recommended)'),

                                                    ]),
                                                Forms\Components\Section::make('Physical Attributes')
                                                    ->icon('heroicon-o-scale')
                                                    ->schema([
                                                        Forms\Components\Grid::make()
                                                            ->schema([
                                                                Forms\Components\TextInput::make('age')
                                                                    ->required()
                                                                    ->numeric()
                                                                    ->minValue(0)
                                                                    ->suffix('months')
                                                                    ->columnSpan(['md' => 1]),

                                                                Forms\Components\TextInput::make('weight')
                                                                    ->required()
                                                                    ->numeric()
                                                                    ->minValue(0)
                                                                    ->suffix('kg')
                                                                    ->columnSpan(['md' => 1]),

                                                                Forms\Components\Select::make('gender')
                                                                    ->options([
                                                                        'male' => 'Male',
                                                                        'female' => 'Female',
                                                                        'unknown' => 'Unknown'
                                                                    ])
                                                                    ->required()
                                                                    ->native(false)
                                                                    ->columnSpan(['md' => 1]),
                                                            ])
                                                            ->columns(3),
                                                    ]),
                                                Forms\Components\Section::make('Health Information')
                                                    ->icon('heroicon-o-heart')
                                                    ->schema([
                                                        Forms\Components\Grid::make()
                                                            ->schema([
                                                                Forms\Components\Select::make('health_status')
                                                                    ->options([
                                                                        'excellent' => 'Excellent',
                                                                        'good' => 'Good',
                                                                        'fair' => 'Fair',
                                                                        'poor' => 'Poor',
                                                                        'critical' => 'Critical'
                                                                    ])
                                                                    ->required()
                                                                    ->native(false)
                                                                    ->columnSpan(['md' => 1]),

                                                                Forms\Components\Toggle::make('vaccination_status')
                                                                    ->label('Vaccinated')
                                                                    ->required()
                                                                    ->onColor('success')
                                                                    ->offColor('danger')
                                                                    ->inline(false)
                                                                    ->columnSpan(['md' => 1]),
                                                            ])
                                                            ->columns(2),

                                                        Forms\Components\Textarea::make('description')
                                                            ->required()
                                                            ->maxLength(500)
                                                            ->rows(4)
                                                            ->placeholder('Detailed health notes and observations')
                                                            ->columnSpanFull()
                                                            ->hint('Max 500 characters'),
                                                    ]),
                                                Forms\Components\Section::make('Financial Information')
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
                                            ])
                                            ->createOptionUsing(function (array $data) {
                                                return Animals::create($data)->id;
                                            }),
                                        Forms\Components\TextInput::make('quantity')
                                            ->numeric()
                                            ->default(1)
                                            ->required()
                                            ->columnSpan(['md' => 1]),
                                        Forms\Components\TextInput::make('unit_price')
                                            ->label('Purchase Price')
                                            ->numeric()
                                            ->required()
                                            ->prefix('Rp')
                                            ->columnSpan(['md' => 1]),
                                    ])
                                    ->columns(2),
                            ])
                            ->columns(1)
                            ->itemLabel(function (array $state): ?string {
                                if (!isset($state['type'])) {
                                    return 'New Item';
                                }

                                if ($state['type'] === 'product') {
                                    return isset($state['product_id'])
                                        ? (Product::withoutGlobalScopes()->find($state['product_id'])?->name ?? 'Product #' . $state['product_id'])
                                        : 'Select Product';
                                }

                                if ($state['type'] === 'animal') {
                                    return isset($state['animals_id'])
                                        ? (Animals::withoutGlobalScopes()->find($state['animals_id'])?->name ?? 'Animal #' . $state['animals_id'])
                                        : 'Select Animal';
                                }

                                return 'Select Item';
                            })
                            ->collapsible()
                            ->addActionLabel('Add Item')
                            ->defaultItems(1),
                    ])
                    ->collapsible(),

                // SECTION 3: SUMMARY AND NOTES
                Forms\Components\Section::make('Summary')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('total_amount')
                                    ->label('Total Amount')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->columnSpan(['md' => 1]),

                                Forms\Components\Textarea::make('note')
                                    ->label('Notes')
                                    ->maxLength(500)
                                    ->rows(2)
                                    ->columnSpan(['md' => 2])
                                    ->placeholder('Additional information about this purchase'),
                            ])
                            ->columns(3),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('purchase_number')
                    ->label('Purchase #')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Purchases $record) => $record->supplier->name)
                    ->color('primary')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('purchase_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable()
                    ->alignCenter()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Amount')
                    ->numeric()
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd()
                    ->color(fn(Purchases $record) => $record->status === 'cancelled' ? 'danger' : 'success')
                    ->weight('medium'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'ordered',
                        'success' => 'received',
                        'danger' => 'cancelled',
                    ])
                    ->icons([
                        'heroicon-o-pencil' => 'draft',
                        'heroicon-o-clock' => 'ordered',
                        'heroicon-o-check-circle' => 'received',
                        'heroicon-o-x-circle' => 'cancelled',
                    ])
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'ordered' => 'Ordered',
                        'received' => 'Received',
                        'cancelled' => 'Cancelled',
                    ])
                    ->label('Status Filter'),

                Tables\Filters\SelectFilter::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->label('Supplier Filter'),

                Tables\Filters\Filter::make('purchase_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('purchase_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('purchase_date', '<=', $date),
                            );
                    })
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
            ])
            ->defaultSort('purchase_date', 'desc')
            ->emptyStateHeading('No purchases yet')
            ->emptyStateDescription('Once you create your first purchase, it will appear here.')
            ->emptyStateIcon('heroicon-o-shopping-bag')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create Purchase')
                    ->icon('heroicon-o-plus'),
            ])
            ->deferLoading()
            ->striped()
            ->poll('10s');
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
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchases::route('/create'),
        ];
    }
}
