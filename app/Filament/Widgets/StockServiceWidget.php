<?php

namespace App\Filament\Widgets;

use App\Models\Hotel;
use App\Models\Breeding;
use App\Models\Grooming;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StockServiceWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        // Breeding stats - changed to use capacity like hotels
        $totalBreedings = Breeding::count();
        $availableBreedings = Breeding::where('stock', '>', 0)->count();
        $fullBreedings = Breeding::where('stock', 0)->count();
        $activeBreedings = Breeding::where('is_active', true)->count();

        // Grooming stats - changed to use stock like hotels
        $totalGroomings = Grooming::count();
        $availableGroomings = Grooming::where('stock', '>', 0)->count();
        $fullGroomings = Grooming::where('stock', 0)->count();
        $activeGroomings = Grooming::where('is_active', true)->count();

        // Hotel stats
        $totalHotels = Hotel::count();
        $availableHotels = Hotel::where('capacity', '>', 0)->count();
        $fullHotels = Hotel::where('capacity', 0)->count();
        $activeHotels = Hotel::where('is_active', true)->count();

        return [
            Stat::make('Breeding Services', $totalBreedings)
                ->description($availableBreedings . ' available, ' . $fullBreedings . ' full, ' . 'Active: ' . $activeBreedings . ', Inactive: ' . ($totalBreedings - $activeBreedings))
                ->descriptionIcon($fullBreedings > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($fullBreedings > 0 ? 'warning' : 'success'),

            Stat::make('Grooming Services', $totalGroomings)
                ->description($availableGroomings . ' available, ' . $fullGroomings . ' full, ' . 'Active: ' . $activeGroomings . ', Inactive: ' . ($totalGroomings - $activeGroomings))
                ->descriptionIcon($fullGroomings > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($fullGroomings > 0 ? 'warning' : 'success'),

            Stat::make('Hotel Services', $totalHotels)
                ->description($availableHotels . ' available, ' . $fullHotels . ' full, ' . 'Active: ' . $activeHotels . ', Inactive: ' . ($totalHotels - $activeHotels))
                ->descriptionIcon($fullHotels > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($fullHotels > 0 ? 'warning' : 'success'),
        ];
    }
}
