<?php

namespace App\Filament\Widgets;

use App\Models\Purchases;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PurchaseWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {

        // Purchases stats
        $totalPurchasing = Purchases::count();
        $ordered = Purchases::where('status', 'ordered')->count();
        $received = Purchases::where('status', 'received')->count();
        $cancelled = Purchases::where('status', 'cancelled')->count();

        return [

            Stat::make('Total Purchases', $totalPurchasing)
                ->description($ordered . ' ordered, ' . $received . ' received, ' . $cancelled . ' cancelled')
                ->descriptionIcon($cancelled > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color('warning')
                ->extraAttributes(['class' => ' text-center'])




        ];
    }
}
