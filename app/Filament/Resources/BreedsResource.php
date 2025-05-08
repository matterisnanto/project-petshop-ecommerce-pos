<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Breeds;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CategoryAnimals;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BreedsResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BreedsResource\RelationManagers;

class BreedsResource extends Resource
{
    protected static ?string $model = Breeds::class;

    protected static ?string $navigationLabel = 'Breeds';
    protected static ?string $modelLabel = 'Breeds';
    protected static ?string $pluralModelLabel = 'Breeds';
    protected static ?string $navigationGroup = 'Animals Resource';
    protected static ?int $navigationSort = 12;

    public static function getNavigationIcon(): string
    {
        return 'lucide-dna';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Breed Information')
                    ->description('Provide details about the animal breed')
                    ->icon('lucide-dna')  // More vibrant icon
                    ->collapsible()  // Allows section to be collapsed
                    ->columns(2)
                    ->schema([
                        // Animal Category Select with enhanced UI
                        Forms\Components\Select::make('category_animals_id')
                            ->relationship('categoryAnimals', 'name')
                            ->label('Animal Category')
                            ->searchable()
                            ->preload()
                            ->prefixIcon('heroicon-o-circle-stack')
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

                        // Breed Name with visual feedback
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

                        // Slug field with copy button
                        Forms\Components\TextInput::make('slug')
                            ->label('URL Identifier')
                            ->required()
                            ->readOnly()
                            ->maxLength(255)
                            ->helperText('Auto-generated from  name')
                            ->columnSpan(['md' => 2]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('categoryAnimals.name')
                ->badge()
                ->color('primary')
                ->alignCenter(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
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
            'index' => Pages\ListBreeds::route('/'),
            'create' => Pages\CreateBreeds::route('/create'),
        ];
    }
}