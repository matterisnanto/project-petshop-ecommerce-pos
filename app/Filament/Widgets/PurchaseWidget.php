<?php

namespace App\Filament\Widgets;

use App\Models\Purchase;
use App\Models\Purchases;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PurchaseWidget extends BaseWidget
{
    use HasWidgetShield;
    protected static ?int $sort = 3;

    protected function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {

        // Purchases stats
        $totalPurchasing = Purchase::count();
        $ordered = Purchase::where('status', 'ordered')->count();
        $received = Purchase::where('status', 'received')->count();
        $cancelled = Purchase::where('status', 'cancelled')->count();

        return [

            Stat::make('Total Purchases', $totalPurchasing)
                ->description($ordered . ' ordered, ' . $received . ' received, ' . $cancelled . ' cancelled')
                ->descriptionIcon($cancelled > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color('warning')
                ->extraAttributes(['class' => ' text-center'])




        ];
    }
}
