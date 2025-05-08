<?php

namespace App\Filament\Widgets;

use App\Models\Hotel;
use App\Models\Animals;
use App\Models\Product;
use App\Models\Breeding;
use App\Models\Grooming;
use App\Models\Purchases;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StockProductAnimals extends BaseWidget
{
    protected static ?int $sort = 1;
    protected ?string $heading = 'Stock Status';

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        // Product stats
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stock', '<', 5)->where('stock', '>', 0)->count();
        $outOfStockProducts = Product::where('stock', 0)->count();

        // Animals stats
        $totalAnimals = Animals::count();
        $activeAnimals = Animals::where('is_active', true)->count();

        return [
            Stat::make('Total Products', $totalProducts)
                ->description($lowStockProducts . ' low stock, ' . $outOfStockProducts . ' out of stock')
                ->descriptionIcon($outOfStockProducts > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($outOfStockProducts > 0 ? 'warning' : 'success'),

            Stat::make('Total Animals', $totalAnimals)
                ->description(' For adoption: ' . $activeAnimals . ', number of animals kept: ' . ($totalAnimals - $activeAnimals))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
