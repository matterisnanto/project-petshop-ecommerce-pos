<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\BarChartWidget;

class TopProductsChart extends ChartWidget
{
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = '4';
    protected static ?string $heading = '5 Produk Terlaris Bulan Ini';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $products = Order::select([
            'products.name',
            DB::raw('SUM(order.quantity) as total_quantity'),
            DB::raw('SUM(order.quantity * order.unit_price) as total_revenue')
        ])
            ->join('products', 'products.id', '=', 'order.product_id')
            ->whereBetween('order.created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Terjual',
                    'data' => $products->pluck('total_quantity')->toArray(),
                    'backgroundColor' => '#3b82f6',
                ],
                [
                    'label' => 'Total Pendapatan (Rp)',
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
                        'text' => 'Jumlah Terjual'
                    ],
                    'beginAtZero' => true,
                ],
                'y1' => [
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Pendapatan (Rp)'
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
