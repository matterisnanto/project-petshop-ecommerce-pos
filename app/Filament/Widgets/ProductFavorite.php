<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Product;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ProductFavorite extends BaseWidget
{
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Produk favorit';
    public function table(Table $table): Table
    {
        $productQuery = Product::query()
            ->withCount('order')
            ->orderBy('order_count')
            ->take(10);
        return $table
            ->query($productQuery)
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                ->label('Photo')
                ->square(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order_count')
                    ->label('Dipesan')
                    ->searchable(),

            ])
            ->defaultPaginationPageOption(5);
    }
}