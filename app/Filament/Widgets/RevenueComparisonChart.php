<?php

namespace App\Filament\Widgets;

use App\Models\POSTransaction;
use App\Models\OlshopTransaction;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class RevenueComparisonChart extends ChartWidget
{
    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = '4';
    protected static ?string $heading = 'Perbandingan Pendapatan Channel';
    protected static ?int $contentHeight = 300; // Changed from maxHeight

    protected function getData(): array
    {
        $posData = Trend::model(PosTransaction::class)
            ->between(now()->subDays(30), now())
            ->perDay()
            ->sum('total_price');

        $olshopData = Trend::model(OlshopTransaction::class)
            ->between(now()->subDays(30), now())
            ->perDay()
            ->sum('grand_total_amount');

        return [
            'datasets' => [
                [
                    'label' => 'POS (Offline)',
                    'data' => $posData->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Online Shop',
                    'data' => $olshopData->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $posData->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "Rp" + value.toLocaleString(); }'
                    ],
                ],
            ],
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { 
                            return context.dataset.label + ": Rp" + context.raw.toLocaleString(); 
                        }'
                    ]
                ]
            ],
        ];
    }
}
