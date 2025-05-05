<?php

namespace App\Filament\Resources\PurchaseReturnResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Product;
use App\Models\Animals;

class ReturnItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'returnItems';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->options([
                        'product' => 'Product',
                        'animal' => 'Animal',
                    ])
                    ->required()
                    ->live(),
                Forms\Components\Select::make('product_id')
                    ->options(Product::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->visible(fn($get) => $get('type') === 'product'),
                Forms\Components\Select::make('animals_id')
                    ->options(Animals::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->visible(fn($get) => $get('type') === 'animal'),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->default(1),
                Forms\Components\TextInput::make('unit_price')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('reason')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('product.name')
                    ->visible(fn($record) => $record->type === 'product'),
                Tables\Columns\TextColumn::make('animals.name')
                    ->visible(fn($record) => $record->type === 'animal'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('unit_price')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('reason'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
