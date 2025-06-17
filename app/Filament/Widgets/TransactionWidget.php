<?php

namespace App\Filament\Widgets;

use App\Models\OlshopTransaction;
use App\Models\POSTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionWidget extends BaseWidget
{
    protected static ?int $sort = 6;
    protected ?string $heading = 'Transactions Analysis';
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalOlshop = OlshopTransaction::count();
        $mustVerify = OlshopTransaction::where('is_paid', false)->count();
        $verified = OlshopTransaction::where('is_paid', true)
            ->where('package_resi_number', 'Being Processed')
            ->count();
        $onCourier = OlshopTransaction::where('is_paid', true)
            ->where('package_resi_number', '!=', 'Being Processed')
            ->where('package_resi_number', '!=', 'Success')
            ->count();
        $success = OlshopTransaction::where('package_resi_number', 'Success')->count();
        $revenueOlshop = OlshopTransaction::sum('grand_total_amount');

        $totalPos = PosTransaction::count();
        $today = PosTransaction::whereDate('created_at', today())->count();
        $revenuePos = PosTransaction::sum('total_price');

        return [
            Stat::make('Total Olshop Transactions ', $totalOlshop)
                ->description($mustVerify . ' must verify, ' . $verified . ' verified, ' . $onCourier . ' on courier, ' . $success . ' success')
                ->descriptionIcon($mustVerify > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($mustVerify > 0 ? 'warning' : 'success')
                ->chart($this->getWeeklyTrendOlshop()),

            Stat::make('Total Revenue', 'Rp ' . number_format($revenueOlshop, 0, ',', '.'))
                ->description('From all online transactions')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),
            Stat::make('Total POS Transactions', $totalPos)
                ->description($today . ' transactions today')
                ->descriptionIcon($today > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($today > 0 ? 'success' : 'gray')
                ->chart($this->getWeeklyTrendPos()),

            Stat::make('Total Revenue', 'Rp ' . number_format($revenuePos, 0, ',', '.'))
                ->description('From all POS transactions')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),
        ];
    }

    protected function getWeeklyTrendOlshop(): array
    {
        $dataOlshop = OlshopTransaction::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->selectRaw('DAYNAME(created_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->pluck('count', 'day')
            ->toArray();

        return [
            'Monday' => $dataOlshop['Monday'] ?? 0,
            'Tuesday' => $dataOlshop['Tuesday'] ?? 0,
            'Wednesday' => $dataOlshop['Wednesday'] ?? 0,
            'Thursday' => $dataOlshop['Thursday'] ?? 0,
            'Friday' => $dataOlshop['Friday'] ?? 0,
            'Saturday' => $dataOlshop['Saturday'] ?? 0,
            'Sunday' => $dataOlshop['Sunday'] ?? 0,
        ];
    }

    protected function getWeeklyTrendPos(): array
    {
        $dataPos = PosTransaction::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->selectRaw('DAYNAME(created_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->pluck('count', 'day')
            ->toArray();

        return [
            'Monday' => $dataPos['Monday'] ?? 0,
            'Tuesday' => $dataPos['Tuesday'] ?? 0,
            'Wednesday' => $dataPos['Wednesday'] ?? 0,
            'Thursday' => $dataPos['Thursday'] ?? 0,
            'Friday' => $dataPos['Friday'] ?? 0,
            'Saturday' => $dataPos['Saturday'] ?? 0,
            'Sunday' => $dataPos['Sunday'] ?? 0,
        ];
    }
}
