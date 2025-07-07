<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\SalesOverview;
use App\Filament\Widgets\TopProductsChart;
use App\Filament\Widgets\StockStatusWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\RevenueComparisonChart;
use App\Filament\Widgets\RecentTransactionsTable;
use App\Filament\Widgets\ServicePerformanceWidget;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;


class Dashboard extends BaseDashboard
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Dashboard Analysis';

    protected static ?string $title = 'Petshop Analytics Dashboard';

    // protected function getHeaderWidgets(): array
    // {
    //     return [
    //         SalesOverview::class,
    //         RevenueComparisonChart::class,
    //         TopProductsChart::class,
    //         RecentTransactionsTable::class,
    //         StockStatusWidget::class,
    //         ServicePerformanceWidget::class,
    //     ];
    // }

    public function getColumns(): int|string|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 3,
            'lg' => 4,
            'xl' => 6,
            '2xl' => 8,
        ];
    }
}
