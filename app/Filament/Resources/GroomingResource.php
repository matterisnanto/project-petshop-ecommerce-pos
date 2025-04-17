<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroomingResource\Pages;
use App\Filament\Resources\GroomingResource\RelationManagers;
use App\Models\Grooming;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GroomingResource extends Resource
{
    protected static ?string $model = Grooming::class;

    protected static ?string $navigationLabel = 'Grooming';
    protected static ?string $modelLabel = 'Grooming';
    protected static ?string $pluralModelLabel = 'Grooming';
    protected static ?string $navigationGroup = 'Service Resource';
    protected static ?int $navigationSort = 20;

    public static function getNavigationIcon(): string
    {
        return 'lucide-shower-head';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('category_animals_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('category_grooming_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('photo')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('stock')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('purchase_price')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('selling_price')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
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
