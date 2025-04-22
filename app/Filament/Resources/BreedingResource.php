<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use App\Models\Breeding;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BreedingResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BreedingResource\RelationManagers;

class BreedingResource extends Resource
{
    protected static ?string $model = Breeding::class;

    protected static ?string $navigationLabel = 'Breeding';
    protected static ?string $modelLabel = 'Breeding';
    protected static ?string $pluralModelLabel = 'Breeding';
    protected static ?string $navigationGroup = 'Service Resource';
    protected static ?int $navigationSort = 12;

    public static function getNavigationIcon(): string
    {
        return 'lucide-baby';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('Primary details about the breeding')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('slug', Breeding::generateUniqueSlug($state));
                            })
                            ->live(onBlur: true)
                            ->maxLength(100)
                            ->columnSpan(2)
                            ->label('Breeding Name'),

                        Forms\Components\TextInput::make('slug')
                            ->label('URL Identifier')
                            ->required()
                            ->readOnly()
                            ->columnSpan(2)
                            ->maxLength(255)
                            ->helperText('Auto-generated from product name'),

                        Forms\Components\Select::make('category_animals_id')
                            ->relationship('categoryAnimals', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Animal Category'),

                        Forms\Components\Select::make('breeds_id')
                            ->relationship('breeds', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Breed'),

                        Forms\Components\FileUpload::make('photo')
                            ->image()
                            ->directory('breeding-photos')
                            ->imageEditor()
                            ->columnSpanFull()
                            ->label('Breeding Photo'),

                        Forms\Components\Textarea::make('description')
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpan(2),
                    ])->columns(2),
                Forms\Components\Section::make('Breeding Packages')
                    ->description('Available Breeding service packages')
                    ->schema([
                        Forms\Components\Repeater::make('breedingPackage')
                            ->relationship('breedingPackage')
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
                Forms\Components\Section::make('Inventory & Pricing')
                    ->description('Stock and financial information')
                    ->schema([
                        Forms\Components\TextInput::make('stock')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->label('Available Stock'),

                        Forms\Components\TextInput::make('purchase_price')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->default(null)
                            ->label('Purchase Price'),

                        Forms\Components\TextInput::make('selling_price')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->label('Selling Price'),

                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->inline(false)
                            ->onColor('success')
                            ->offColor('danger')
                            ->label('Active Breeding'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category_animals_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('breeds_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('photo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('selling_price')
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
            'index' => Pages\ListBreedings::route('/'),
            'create' => Pages\CreateBreeding::route('/create'),
            'edit' => Pages\EditBreeding::route('/{record}/edit'),
        ];
    }
}
