<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Animals;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AnimalsResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AnimalsResource\RelationManagers;
use App\Models\Breeds;
use App\Models\CategoryAnimals;

class AnimalsResource extends Resource
{
    protected static ?string $model = Animals::class;

    protected static ?string $navigationLabel = 'Animals';
    protected static ?string $modelLabel = 'Animals';
    protected static ?string $pluralModelLabel = 'Animals';
    protected static ?string $navigationGroup = 'Animals Resource';
    protected static ?int $navigationSort = 11;

    // protected static ?string $navigationIcon = 'pawprint';
    public static function getNavigationIcon(): string
    {
        return 'lucide-paw-print';
    }

    protected static ?string $navigationBadgeTooltip = 'Active Animals for sale';

    public static function getNavigationBadge(): ?string
    {
        return (string) Animals::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('Basic Information')
                ->description('Primary details about the animal')
                ->icon('heroicon-o-identification')
                ->collapsible()
                ->columns(2)
                ->schema([
                        // Name and Barcode Row - Enhanced UI
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('slug', Animals::generateUniqueSlug($state));
                                    })
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255)
                                    ->columnSpan(['md' => 2])
                                    ->placeholder('Enter animal name')
                                    ->hint('The official name of the animal')
                                    ->label('Animal Name')
                                    ->prefixIcon('heroicon-o-tag'),
                                    
                                Forms\Components\Hidden::make('slug'),
                                
                                Forms\Components\TextInput::make('barcode')
                                    ->maxLength(255)
                                    ->columnSpan(['md' => 1])
                                    ->hint('Unique identifier')
                                    ->prefixIcon('lucide-barcode')
                                    ->label('Barcode ID'),
                            ])
                            ->columns(3),
                        
                        // Category and Breed Selects - Enhanced UI
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Select::make('category_animals_id')
                                    ->relationship('categoryAnimals', 'name')
                                    ->label('Animal Category')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->prefixIcon('heroicon-o-circle-stack')
                                    ->placeholder('Select animal category')
                                    ->helperText('Choose or create new category')
                                    ->columnSpan(['md' => 1])
                                    ->createOptionForm([
                                        // Keep existing create option form exactly as is
                                        Forms\Components\Section::make('New Category Animals')
                                            ->icon('heroicon-o-circle-stack')
                                            ->collapsible()
                                            ->schema([
                                                Forms\Components\Grid::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('name')
                                                            ->label('Category Name')
                                                            ->afterStateUpdated(function (Set $set, $state) {
                                                                $set('slug', CategoryAnimals::generateUniqueSlug($state));
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
                                            ->columns(2),
                                    ]),
                                    
                                Forms\Components\Select::make('breeds_id')
                                    ->relationship('breeds', 'name')
                                    ->label('Animal Breed')
                                    ->native(false)
                                    ->searchable()
                                    ->preload()
                                    ->prefixIcon('lucide-dna')
                                    ->placeholder('Select animal breed')
                                    ->helperText('Choose or create new breed')
                                    ->columnSpan(['md' => 1])
                                    ->createOptionForm([
                                        // Keep existing create option form exactly as is
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                Forms\Components\Section::make('New Breed')
                                                    ->icon('lucide-dna')
                                                    ->collapsible()
                                                    ->columns(2)
                                                    ->schema([
                                                        Forms\Components\Select::make('category_animals_id')
                                                            ->relationship('categoryAnimals', 'name')
                                                            ->label('Animal Category')
                                                            ->searchable()
                                                            ->preload()
                                                            ->native(false)
                                                            ->placeholder('Select or create a category')
                                                            ->createOptionForm([
                                                                Forms\Components\Section::make('New Category Animals')
                                                                    ->icon('heroicon-o-tag')
                                                                    ->collapsible()
                                                                    ->schema([
                                                                        Forms\Components\Grid::make()
                                                                            ->schema([
                                                                                Forms\Components\TextInput::make('name')
                                                                                    ->label('Category Name')
                                                                                    ->afterStateUpdated(function (Set $set, $state) {
                                                                                        $set('slug', CategoryAnimals::generateUniqueSlug($state));
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
                                                                    ->columns(2),
                                                            ])
                                                            ->columnSpanFull(),
    
                                                        Forms\Components\TextInput::make('name')
                                                            ->label('Breed Name')
                                                            ->afterStateUpdated(function (Set $set, $state) {
                                                                $set('slug', Breeds::generateUniqueSlug($state));
                                                            })
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->maxLength(255)
                                                            ->columnSpanFull()
                                                            ->prefixIcon('lucide-dna'),
    
                                                        Forms\Components\TextInput::make('slug')
                                                            ->label('URL Identifier')
                                                            ->required()
                                                            ->readOnly()
                                                            ->maxLength(255)
                                                            ->helperText('Auto-generated from name')
                                                            ->columnSpan(['md' => 2]),
                                                    ])
                                            ])
                                    ]),
                            ])
                            ->columns(2),
                        
                        // Main Photo Upload - Enhanced UI
                        Forms\Components\FileUpload::make('thumbnail')
                            ->label('Main Photo')
                            ->image()
                            ->required()
                            ->directory('animal-thumbnails')
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->panelLayout('integrated')
                            ->hint('Primary display image (1:1 ratio recommended)')
                            ->columnSpanFull(),
                            
                        // Additional Photos Repeater - Enhanced UI
                        Forms\Components\Repeater::make('photos')
                            ->relationship('animalsPhotos')
                            ->label('Additional Photos')
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->image()
                                    ->directory('animal-gallery')
                                    ->imageEditor()
                                    ->panelLayout('integrated')
                                    ->required(),
                        ])
                        ->grid(3)
                        ->defaultItems(1)
                        ->columnSpanFull()
                        ->createItemButtonLabel('Add another photo'),
                    ]),
                
                // Physical Attributes Section - Keep exactly as is
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
    
                // Health Information Section - Keep exactly as is
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
                                    
                                Forms\Components\TextInput::make('stock')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefixIcon('heroicon-o-archive-box')
                                    ->columnSpan(['md' => 1]),

                                Forms\Components\Toggle::make('vaccination_status')
                                    ->label('Vaccinated')
                                    ->required()
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->inline(false)
                                    ->columnSpan(['md' => 1]),
                            ])
                            ->columns(3),
    
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(500)
                            ->rows(4)
                            ->placeholder('Detailed health notes and observations')
                            ->columnSpanFull()
                            ->hint('Max 500 characters'),
                    ]),
    
                // Financial Information Section - Keep exactly as is
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
    
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Available for sale')
                                    ->required()
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->inline(false)
                                    ->columnSpan(['md' => 1]),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Photo')
                    ->circular()
                    ->size(40),

                Tables\Columns\TextColumn::make('name')
                        ->label('Name')
                        ->searchable()
                        ->sortable()
                        ->weight('medium')
                        ->description(fn(Animals $record): string => $record->breeds->name ?? ''),

                Tables\Columns\TextColumn::make('categoryAnimals.name')
                    ->label('Category')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('age')
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => "{$state} months"),

                //Tables\Columns\TextColumn::make('weight')
                    //->sortable()
                    //->suffix(' kg')
                   // ->toggleable(),

                Tables\Columns\TextColumn::make('gender')
                    ->label('Gender')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'male' => 'info',
                        'female' => 'warning',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('health_status')
                    ->label('Health')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'excellent' => 'success',
                        'good' => 'primary',
                        'fair' => 'warning',
                        'poor' => 'danger',
                        'critical' => 'danger',
                        default => 'gray',
                    }),
                    
                //Tables\Columns\TextColumn::make('stock')
                    //->searchable(),

                Tables\Columns\IconColumn::make('vaccination_status')
                    ->label('Vaccinated')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark'),

                Tables\Columns\TextColumn::make('selling_price')
                    ->label('selling price')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        // Format nilai sebagai Rupiah
                        return 'Rp ' . number_format($state, 0, ',', '.');
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Available')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_animals_id')
                    ->relationship('categoryAnimals', 'name')
                    ->label('Category')
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'unknown' => 'Unknown'
                    ]),

                Tables\Filters\SelectFilter::make('health_status')
                    ->options([
                        'excellent' => 'Excellent',
                        'good' => 'Good',
                        'fair' => 'Fair',
                        'poor' => 'Poor',
                        'critical' => 'Critical'
                    ]),

                Tables\Filters\TernaryFilter::make('vaccination_status')
                    ->label('Vaccination Status'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Availability'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    ->color('primary') // Blue color
                    ->tooltip('View'),
                    
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit'),
                    
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Delete'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->icon('heroicon-o-trash'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->groups([
                Tables\Grouping\Group::make('categoryAnimals.name')
                ->label('Category')
                ->collapsible(),
            Tables\Grouping\Group::make('gender')
                ->collapsible(),
            Tables\Grouping\Group::make('health_status')
                ->label('Health Status')
                ->collapsible(),
        ])
        ->persistFiltersInSession()
        ->persistSearchInSession()
        ->striped()
        ->paginated([10, 25, 50, 100])
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
            'index' => Pages\ListAnimals::route('/'),
            'create' => Pages\CreateAnimals::route('/create'),
            // 'edit' => Pages\EditAnimals::route('/{record}/edit'),
        ];
    }
}