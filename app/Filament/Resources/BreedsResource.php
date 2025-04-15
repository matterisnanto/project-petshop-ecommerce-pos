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
    protected static ?int $navigationSort = 8;

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
                    ->icon('heroicon-o-information-circle')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('category_animals_id')
                            ->relationship('categoryAnimals', 'name')
                            ->label('Animals Category')
                            ->default(null)
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('slug', CategoryAnimals::generateUniqueSlug($state));
                                    })
                                    ->required()
                                    ->live(onBlur: true)
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->readOnly()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('description')
                                    ->maxLength(255)
                                    ->default(null),
                                Forms\Components\FileUpload::make('icon')
                                    ->image()
                                    ->columnSpan('full')
                                    ->default(null),
                            ])
                            ->columnSpanFull()
                            ->default(null),
                        Forms\Components\TextInput::make('name')
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('slug', Breeds::generateUniqueSlug($state));
                            })
                            ->required()
                            ->live(onBlur: true)
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->readOnly()
                            ->columnSpanFull()
                            ->maxLength(255),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category_animals_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
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
            'index' => Pages\ListBreeds::route('/'),
            'create' => Pages\CreateBreeds::route('/create'),
            'edit' => Pages\EditBreeds::route('/{record}/edit'),
        ];
    }
}
