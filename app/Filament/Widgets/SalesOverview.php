<?php

namespace App\Filament\Widgets;

use App\Models\PosTransaction;
use App\Models\OlshopTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class SalesOverview extends BaseWidget
{
    protected static ?int $sort = 10;
    protected function getStats(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $posToday = PosTransaction::whereDate('created_at', $today)->count();
        $olshopToday = OlshopTransaction::whereDate('created_at', $today)->count();
        $posRevenueToday = PosTransaction::whereDate('created_at', $today)->sum('total_price');
        $olshopRevenueToday = OlshopTransaction::whereDate('created_at', $today)->sum('grand_total_amount');

        $posYesterday = PosTransaction::whereDate('created_at', $yesterday)->count();
        $olshopYesterday = OlshopTransaction::whereDate('created_at', $yesterday)->count();
        $posRevenueYesterday = PosTransaction::whereDate('created_at', $yesterday)->sum('total_price');
        $olshopRevenueYesterday = OlshopTransaction::whereDate('created_at', $yesterday)->sum('grand_total_amount');

        $transactionDiff = $this->calculatePercentageDiff(
            $posYesterday + $olshopYesterday,
            $posToday + $olshopToday
        );

        $revenueDiff = $this->calculatePercentageDiff(
            $posRevenueYesterday + $olshopRevenueYesterday,
            $posRevenueToday + $olshopRevenueToday
        );

        return [
            Stat::make('Total Transactions', $posToday + $olshopToday)
                ->description($this->getDescriptionText($transactionDiff))
                ->descriptionIcon($this->getDescriptionIcon($transactionDiff))
                ->color($this->getDescriptionColor($transactionDiff)),

            Stat::make('Total Revenue', number_format($posRevenueToday + $olshopRevenueToday, 0, ',', '.'))
                ->description($this->getDescriptionText($revenueDiff))
                ->descriptionIcon($this->getDescriptionIcon($revenueDiff))
                ->color($this->getDescriptionColor($revenueDiff)),

            Stat::make('Average Transaction Value', number_format(
                ($posToday + $olshopToday) > 0
                    ? ($posRevenueToday + $olshopRevenueToday) / ($posToday + $olshopToday)
                    : 0,
                0,
                ',',
                '.'
            ))
                ->description('Today')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
        ];
    }

    private function calculatePercentageDiff($old, $new): float
    {
        if ($old == 0) return 0;
        return (($new - $old) / $old) * 100;
    }

    private function getDescriptionText(float $diff): string
    {
        if ($diff > 0) {
            return number_format(abs($diff), 1) . '% increase from yesterday';
        } elseif ($diff < 0) {
            return number_format(abs($diff), 1) . '% decrease from yesterday';
        }
        return 'No change from yesterday';
    }

    private function getDescriptionIcon(float $diff): string
    {
        if ($diff > 0) return 'heroicon-m-arrow-trending-up';
        if ($diff < 0) return 'heroicon-m-arrow-trending-down';
        return 'heroicon-m-minus';
    }

    private function getDescriptionColor(float $diff): string
    {
        if ($diff > 0) return 'success';
        if ($diff < 0) return 'danger';
        return 'gray';
    }
}
