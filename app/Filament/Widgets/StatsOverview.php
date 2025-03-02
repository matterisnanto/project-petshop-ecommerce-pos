<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\PosTransaction;
use App\Models\OlshopTransaction;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $product_count = Product::count();
        $omset = PosTransaction::sum('total_price') + OlshopTransaction::sum('total_price');
        return [
            Stat::make('Product', $product_count),
            Stat::make('Omset', number_format($omset,0,",",".")),
            Stat::make('Average time on page', '3:12'),
        ];
    }
}