<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use App\Models\Grooming;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CategoryAnimals;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\GroomingResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\GroomingResource\RelationManagers;

class GroomingResource extends Resource
{
    protected static ?string $model = Grooming::class;

    protected static ?string $navigationLabel = 'Grooming';
    protected static ?string $modelLabel = 'Grooming';
    protected static ?string $pluralModelLabel = 'Grooming';
    protected static ?string $navigationGroup = 'Service Resource';
    protected static ?int $navigationSort = 11;

    public static function getNavigationIcon(): string
    {
        return 'lucide-shower-head';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('Product identification details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(['md' => 2])
                            ->placeholder('Enter product name')
                            ->hint('The full display name of the product'),

                        Forms\Components\TextInput::make('slug')
                            ->label('URL Identifier')
                            ->required()
                            ->readOnly()
                            ->maxLength(255)
                            ->helperText('Auto-generated from product name')
                            ->columnSpan(['md' => 2]),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Grooming Packages')
                    ->description('Available grooming service packages')
                    ->schema([
                        Forms\Components\Repeater::make('groomingPackage')
                            ->relationship('groomingPackage')
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
                Forms\Components\Section::make('Categories')
                    ->description('Product classification')
                    ->schema([
                        Forms\Components\Select::make('category_animals_id')
                            ->relationship('categoryAnimals', 'name')
                            ->label('Animal Category')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->placeholder('Select or create a category')
                            ->createOptionForm([
                                Forms\Components\Section::make('New Animal Category')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Category Name')
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                $set('slug', CategoryAnimals::generateUniqueSlug($state));
                                            })
                                            ->required()
                                            ->live(onBlur: true)
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('slug')
                                            ->label('URL Slug')
                                            ->required()
                                            ->readOnly()
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                        Forms\Components\Textarea::make('description')
                                            ->label('Description')
                                            ->rows(3)
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Forms\Components\FileUpload::make('icon')
                                            ->label('Category Icon')
                                            ->image()
                                            ->directory('animal-category-icons')
                                            ->imageEditor()
                                            ->columnSpanFull(),
                                    ]),
                            ]),


                        Forms\Components\Select::make('category_grooming_id')
                            ->relationship('categoryGrooming', 'name')
                            ->label('Grooming Category')
                            ->placeholder('Select grooming category')
                            ->native(false)
                            ->searchable()
                            ->columnSpan(['md' => 1])
                            ->createOptionForm([
                                Forms\Components\Section::make('New Grooming Category')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(['md' => 2])
                                            ->placeholder('Category name'),

                                        Forms\Components\TextInput::make('slug')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->rules(['alpha_dash'])
                                            ->columnSpan(['md' => 2]),

                                        Forms\Components\Textarea::make('description')
                                            ->columnSpanFull()
                                            ->rows(3)
                                            ->maxLength(1000),

                                        Forms\Components\FileUpload::make('photo')
                                            ->directory('category-photos')
                                            ->image()
                                            ->imageEditor()
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('16:9')
                                            ->columnSpanFull(),
                                    ])
                            ]),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pricing & Inventory')
                    ->schema([
                        Forms\Components\TextInput::make('stock')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefixIcon('heroicon-o-archive-box')
                            ->columnSpan(['md' => 1]),

                        Forms\Components\TextInput::make('purchase_price')
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->columnSpan(['md' => 1]),

                        Forms\Components\TextInput::make('selling_price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->columnSpan(['md' => 1]),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active product')
                            ->inline(false)
                            ->columnSpan(['md' => 1])
                            ->onColor('success')
                            ->offColor('danger'),
                    ])
                    ->columns(4),

                Forms\Components\Section::make('Product Details')
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                            ->label('Product Image')
                            ->directory('product-photos')
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('4:3')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->rows(5)
                            ->placeholder('Detailed product description')
                            ->maxLength(2000),
                    ]),


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
                Tables\Columns\TextColumn::make('category_grooming_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('photo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stock')
                    ->searchable(),
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
            'index' => Pages\ListGroomings::route('/'),
            'create' => Pages\CreateGrooming::route('/create'),
            'edit' => Pages\EditGrooming::route('/{record}/edit'),
        ];
    }
}
