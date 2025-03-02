<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
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
        $omset = PosTransaction::sum('total_price');
        $expense = Expense::sum('amount');
        return [
            Stat::make('Product', $product_count),
            Stat::make('Omset', number_format($omset,0,",",".")),
            Stat::make('Expense', number_format($expense,0,",",".")),
        ];
    }
}