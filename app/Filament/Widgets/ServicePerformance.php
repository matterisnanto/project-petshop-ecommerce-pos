<?php

namespace App\Filament\Widgets;

use App\Models\DetailOrder;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class ServicePerformance extends BaseWidget
{
    use HasWidgetShield;
    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        // Grooming stats
        $groomingOrders = DetailOrder::whereNotNull('grooming_id')->get();
        $groomingCount = $groomingOrders->count();
        $groomingRevenue = $groomingOrders->sum(function ($detail_order) {
            return $detail_order->quantity * $detail_order->unit_price;
        });

        // Breeding stats
        $breedingOrders = DetailOrder::whereNotNull('breeding_id')->get();
        $breedingCount = $breedingOrders->count();
        $breedingRevenue = $breedingOrders->sum(function ($detail_order) {
            return $detail_order->quantity * $detail_order->unit_price;
        });

        // Hotel stats
        $hotelOrders = DetailOrder::whereNotNull('hotel_id')->get();
        $hotelCount = $hotelOrders->count();
        $hotelRevenue = $hotelOrders->sum(function ($detail_order) {
            return $detail_order->quantity * $detail_order->unit_price;
        });




        return [

            Stat::make('Grooming Service', $groomingCount . ' packages')
                ->description('Rp ' . number_format($groomingRevenue, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-scissors')
                ->color('primary'),

            Stat::make('Breeding Service', $breedingCount . ' packages')
                ->description('Rp ' . number_format($breedingRevenue, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-heart')
                ->color('success'),

            Stat::make('Hotel Service', $hotelCount . ' packages')
                ->description('Rp ' . number_format($hotelRevenue, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-home')
                ->color('info'),
        ];
    }
}
