<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;

class TopConsumedProduct extends ChartWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'أكثر المنتجات استهلاكاً';

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $rows = OrderItem::query()
            ->selectRaw('products.name AS product_name, SUM(order_items.quantity) AS total_consumed')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.status', 'confirmed')
            ->groupBy('products.name')
            ->orderByDesc('total_consumed')
            ->limit(10)
            ->get();

        return [
            'labels' => $rows->pluck('product_name')->toArray(),
            'datasets' => [
                [
                    'label' => 'الكمية المستهلكة',
                    'data' => $rows->pluck('total_consumed')->toArray(),
                    'backgroundColor' => [
                        '#FF6384', // Pink
                        '#36A2EB', // Blue
                        '#FFCE56', // Yellow
                        '#4BC0C0', // Teal
                        '#9966FF', // Purple
                        '#FF9F40', // Orange
                        '#C9CBCF', // Gray
                        '#8BC34A', // Green
                        '#E91E63', // Magenta
                        '#00BCD4', // Cyan
                    ],
                ],
            ],
        ];
    }
}
