<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\BarChartWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class TopProductsChart extends ChartWidget
{
    use HasWidgetShield;
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = '4';
    protected static ?string $heading = '5 Best Selling Products This Month';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $products = Order::select([
            'products.name',
            DB::raw('SUM(detail_order.quantity) as total_quantity'),
            DB::raw('SUM(detail_order.quantity * detail_order.unit_price) as total_revenue')
        ])
            ->join('products', 'products.id', '=', 'detail_order.product_id')
            ->whereBetween('detail_order.created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Sold',
                    'data' => $products->pluck('total_quantity')->toArray(),
                    'backgroundColor' => '#3b82f6',
                ],
                [
                    'label' => 'Total Revenue (Rp)',
                    'data' => $products->pluck('total_revenue')->toArray(),
                    'backgroundColor' => '#10b981',
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $products->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Number of Sold'
                    ],
                    'beginAtZero' => true,
                ],
                'y1' => [
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Revenue (Rp)'
                    ],
                    'beginAtZero' => true,
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                    'ticks' => [
                        'callback' => 'function(value) { return "Rp" + value.toLocaleString(); }'
                    ],
                ],
            ],
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { 
                            if(context.datasetIndex === 0) {
                                return "Jumlah: " + context.raw; 
                            } else {
                                return "Pendapatan: Rp" + context.raw.toLocaleString(); 
                            }
                        }'
                    ]
                ]
            ],
        ];
    }
}
