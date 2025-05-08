<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Hotel;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CategoryAnimals;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\HotelResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\HotelResource\RelationManagers;

class HotelResource extends Resource
{
    protected static ?string $model = Hotel::class;

    protected static ?string $navigationLabel = 'Hotel';
    protected static ?string $modelLabel = 'Hotel';
    protected static ?string $pluralModelLabel = 'Hotel';
    protected static ?string $navigationGroup = 'Service Resource';
    protected static ?int $navigationSort = 17;

    public static function getNavigationIcon(): string
    {
        return 'lucide-hotel';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Basic Information Section
                Forms\Components\Section::make('Basic Information')
                    ->icon('heroicon-o-identification')
                    ->description('Provide core details about the animal')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(' Name')
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('slug', Hotel::generateUniqueSlug($state));
                            })
                            ->live(onBlur: true)
                            ->required()
                            ->maxLength(255)
                            ->placeholder('E.g., Package 1 day')
                            ->prefixIcon('heroicon-o-tag')
                            ->columnSpan(['md' => 2]),
                            
                        Forms\Components\TextInput::make('slug')
                            ->label('URL Identifier')
                            ->required()
                            ->readOnly()
                            ->maxLength(255)
                            ->helperText('Auto-generated from animal name')
                            ->prefixIcon('heroicon-o-link')
                            ->columnSpan(['md' => 2]),
                            
                        Forms\Components\Select::make('category_animals_id')
                            ->relationship('categoryAnimals', 'name')
                            ->label('Animal Category')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->prefixIcon('heroicon-o-circle-stack')
                            ->placeholder('Select or create a category')
                            ->createOptionForm([
                                Forms\Components\Section::make('New Animal Category')
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
                                                    ->placeholder('E.g., Dogs, Cats, Birds')
                                                    ->prefixIcon('heroicon-o-tag')
                                                    ->helperText('The display name for this animal category')
                                                    ->columnSpan(['md' => 2]),
    
                                                Forms\Components\TextInput::make('slug')
                                                    ->label('URL Identifier')
                                                    ->required()
                                                    ->readOnly()
                                                    ->maxLength(255)
                                                    ->prefixIcon('heroicon-o-link')
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
                            ->columnSpan(['md' => 2])
                            ->helperText('Select or create animal category'),
                    ]),

                      // Packages Section
                Forms\Components\Section::make('Service Packages')
                ->icon('heroicon-o-gift')
                ->collapsible()
                ->schema([
                    Forms\Components\Repeater::make('hotelPackage')
                        ->relationship('hotelPackage')
                        ->label('Available Packages')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->placeholder('Package name')
                                ->prefixIcon('heroicon-o-cube'),
                                
                            Forms\Components\TextInput::make('price')
                                ->numeric()
                                ->prefix('Rp')
                                ->minValue(0),
                            Forms\Components\Textarea::make('description')
                                ->rows(2)
                                ->maxLength(500)
                                ->placeholder('Package details...'),
                        ])
                        ->columns(3)
                        ->columnSpanFull()
                        ->addActionLabel('Add New Package')
                        ->defaultItems(1)
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                ]),
                    
                // Description Section
                Forms\Components\Section::make('Description')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Animal Description')
                            ->columnSpanFull()
                            ->rows(5)
                            ->placeholder('Describe the animal characteristics, temperament, and special features...')
                            ->maxLength(1000)
                            ->helperText('Max 1000 characters'),
                    ]),
                    
                // Pricing & Availability Section
                Forms\Components\Section::make('Pricing & Availability')
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('price_per_day')
                            ->label('Daily Price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->inputMode('decimal')
                            ->step(1000)
                            ->minValue(0)
                            ->maxValue(1000000)
                            ->placeholder('E.g., 150000'),
                            
                        Forms\Components\TextInput::make('capacity')
                            ->label('Available Quantity')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->step(1)
                            ->suffix('animals')
                            ->placeholder('E.g., 5')
                            ->prefixIcon('heroicon-o-archive-box'),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Available for Rental')
                            ->inline(false)
                            ->onIcon('heroicon-o-check')
                            ->offIcon('heroicon-o-x-mark')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true)
                            ->columnSpan(['md' => 2])
                            ->helperText('Toggle to make this animal available/unavailable'),
                    ]),
                    
              
                    
                // Media Section
                Forms\Components\Section::make('Media')
                    ->icon('heroicon-o-camera')
                    ->collapsible()
                    ->schema([
                        Forms\Components\FileUpload::make('thumbnail')
                            ->label('Animal Photo')
                            ->directory('animal-photos')
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imagePreviewHeight('200')
                            ->maxSize(2048)
                            ->helperText('Upload a clear photo of the animal (max 2MB)')
                            ->columnSpanFull()
                            ->panelLayout('integrated'),
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
                    ->size(50)
                    ->grow(false),
                    
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->wrap()
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('categoryAnimals.name')
                    ->label('Category')
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('price_per_day')
                    ->numeric()
                    ->money('IDR', locale: 'id')
                    ->color('success')
                    ->weight('bold')
                    ->sortable()
                    ->alignCenter(),
                    
                    Tables\Columns\TextColumn::make('capacity')
                    ->label('Room Capacity')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->description(fn ($record) => $record->capacity <= 5 ? 'Limited space!' : 'Available')
                    ->icon(fn ($record) => $record->capacity <= 5 
                        ? 'heroicon-o-exclamation-triangle' 
                        : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->capacity <= 5 
                        ? 'danger' 
                        : ($record->capacity >= 15 ? 'warning' : 'success'))
                    ->weight(fn ($record) => $record->capacity <= 5 ? 'bold' : 'normal')
                    ->tooltip(fn ($record) => $record->capacity <= 5 
                        ? 'Only few rooms left!' 
                        : 'Rooms available')
                    ->formatStateUsing(fn ($state) => "{$state} rooms")
                    ->extraAttributes(['class' => 'py-2']),  // Add vertical padding
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->alignCenter()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignEnd(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignEnd(),
                    
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignEnd(),
            ])
            ->filters([
                // Maintained original empty filters array
            ])
            ->actions([
                //Tables\Actions\ViewAction::make()
                    //->iconButton()
                    //->color('primary') // Blue color
                    //->tooltip('View'),
                    
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
                        ->label('Delete Selected')
                        ->icon('heroicon-o-trash'),
                ]),
            ])
            ->defaultSort('name', 'asc')
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
            'index' => Pages\ListHotels::route('/'),
            'create' => Pages\CreateHotel::route('/create'),
        ];
    }
}