<?php

namespace App\Filament\Widgets;

use App\Models\Grooming;
use App\Models\Breeding;
use App\Models\Hotel;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class SalesPerformanceWidget extends BaseWidget
{
    use HasWidgetShield;
    protected static ?int $sort = 4;
    protected ?string $heading = 'Sales Performance';

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {

        // Product stats
        $productOrders = Order::whereNotNull('product_id')->get();
        $productCount = $productOrders->count();
        $productRevenue = $productOrders->sum(function ($order) {
            return $order->quantity * $order->unit_price;
        });

        // Animal stats
        $animalOrders = Order::whereNotNull('animals_id')->get();
        $animalCount = $animalOrders->count();
        $animalRevenue = $animalOrders->sum(function ($order) {
            return $order->quantity * $order->unit_price;
        });

        return [
            Stat::make('Products Sold', $productCount . ' items')
                ->description('Rp ' . number_format($productRevenue, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning'),

            Stat::make('Animals Sold', $animalCount . ' animals')
                ->description('Rp ' . number_format($animalRevenue, 0, ',', '.'))
                ->descriptionIcon('lucide-paw-print')
                ->color('danger'),
        ];
    }
}
