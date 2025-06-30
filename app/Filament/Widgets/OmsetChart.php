<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Flowframe\Trend\Trend;
use App\Models\POSTransaction;
use Flowframe\Trend\TrendValue;
use App\Models\OlshopTransaction;
use Filament\Widgets\ChartWidget;

class OmsetChart extends ChartWidget
{
    protected static ?string $heading = 'Omset';
    protected static ?int $sort = 11;
    protected int | string | array $columnSpan = '4';
    public ?string $filter = 'today';
    protected static string $color = 'success';

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $dateRange = match ($activeFilter) {
            'today' => [
                'start' => now()->startOfDay(),
                'end' => now()->endOfDay(),
                'period' => 'perHour'
            ],
            'week' => [
                'start' => now()->startOfWeek(),
                'end' => now()->endOfWeek(),
                'period' => 'perDay'
            ],
            'month' => [
                'start' => now()->startOfMonth(),
                'end' => now()->endOfMonth(),
                'period' => 'perDay'
            ],
            'year' => [
                'start' => now()->startOfYear(),
                'end' => now()->endOfYear(),
                'period' => 'perMonth'
            ],
        };

        // Get POS transactions data
        $posData = $this->getTrendData(PosTransaction::class, 'total_price', $dateRange);
        // Get Olshop transactions data
        $olshopData = $this->getTrendData(OlshopTransaction::class, 'grand_total_amount', $dateRange);

        // Combine the data
        $combinedData = $posData->map(function (TrendValue $value, $index) use ($olshopData) {
            $olshopValue = $olshopData[$index] ?? null;
            $combinedAggregate = $value->aggregate + ($olshopValue ? $olshopValue->aggregate : 0);

            return new TrendValue(
                date: $value->date,
                aggregate: $combinedAggregate
                // Removed the format parameter as it's not needed
            );
        });

        $labels = $combinedData->map(function (TrendValue $value) use ($dateRange) {
            $date = Carbon::parse($value->date);

            if ($dateRange['period'] === 'perHour') {
                return $date->format('H:i');
            } elseif ($dateRange['period'] === 'perDay') {
                return $date->format('d M');
            }
            return $date->format('M Y');
        });

        return [
            'datasets' => [
                [
                    'label' => 'Total Omset ' . $this->getFilters()[$activeFilter],
                    'data' => $combinedData->map(fn(TrendValue $value) => $value->aggregate),
                ],
                [
                    'label' => 'POS Omset ' . $this->getFilters()[$activeFilter],
                    'data' => $posData->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#22c55e', // Green for POS
                    'backgroundColor' => '#22c55e',
                ],
                [
                    'label' => 'Olshop Omset ' . $this->getFilters()[$activeFilter],
                    'data' => $olshopData->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#3b82f6', // Blue for Olshop
                    'backgroundColor' => '#3b82f6',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getTrendData(string $model, string $column, array $dateRange)
    {
        $query = Trend::model($model)
            ->between(
                start: $dateRange['start'],
                end: $dateRange['end'],
            );

        if ($dateRange['period'] === 'perHour') {
            $data = $query->perHour();
        } elseif ($dateRange['period'] === 'perDay') {
            $data = $query->perDay();
        } else {
            $data = $query->perMonth();
        }

        return $data->sum($column);
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
