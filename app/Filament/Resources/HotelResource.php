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
    protected static ?int $navigationSort = 12;

    public static function getNavigationIcon(): string
    {
        return 'lucide-hotel';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->icon('heroicon-o-identification')
                    ->description('Provide core details about the animal')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('E.g., Package 1 day')
                            ->prefixIcon('heroicon-o-tag')
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
                                Forms\Components\Section::make('New Category Animals')
                                    ->icon('heroicon-o-circle-stack') // Added icon
                                    ->collapsible() // Make section collapsible
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
                            ->columnSpan(['md' => 2])
                            ->helperText('Select or create animal category'),

                    ]),
                Forms\Components\Section::make('Hotel Packages')
                    ->description('Available hotel service packages')
                    ->schema([
                        Forms\Components\Repeater::make('hotelPackage')
                            ->relationship('hotelPackage')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->placeholder('Package name'),

                                Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0),

                                Forms\Components\Textarea::make('description')
                                    ->rows(2)
                                    ->maxLength(500),
                            ])
                            ->columns(3)
                            ->columnSpanFull()
                            ->addActionLabel('Add Package')
                            ->defaultItems(1),
                    ]),

                Forms\Components\Section::make('Description')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Hotel Description')
                            ->columnSpanFull()
                            ->rows(5)
                            ->placeholder('Describe the hotel characteristics, temperament, and special features...')
                            ->maxLength(1000)
                            ->helperText('Max 1000 characters'),
                    ]),

                Forms\Components\Section::make('Rental Details')
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('price_per_day')
                            ->label('Daily Rental Price')
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
                            ->placeholder('E.g., 5'),
                    ]),

                Forms\Components\Section::make('Media & Status')
                    ->icon('heroicon-o-camera')
                    ->columns(2)
                    ->schema([
                        Forms\Components\FileUpload::make('thumbnail')
                            ->label('hotel Photo')
                            ->directory('hotel-thumbnails')
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imagePreviewHeight('200')
                            ->maxSize(2048)
                            ->helperText('Upload a clear photo of the hotel (max 2MB)')
                            ->columnSpan(['md' => 2]),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Available for Rental?')
                            ->inline(false)
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true)
                            ->columnSpan(['md' => 1]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('photo'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('categoryAnimals')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_per_day')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
                //
            ])
            ->actions([
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
            'index' => Pages\ListHotels::route('/'),
            'create' => Pages\CreateHotel::route('/create'),
        ];
    }
}
