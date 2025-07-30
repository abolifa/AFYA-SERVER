<?php

namespace App\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopCentersConsumption extends ChartWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'أعلى المراكز استهلاكاً';
    protected static ?string $maxHeight = '250px';
    protected int|string|array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'bar';
    } // or 'bar' depending on your Chart.js version

    protected function getData(): array
    {
        $rows = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('centers', 'centers.id', '=', 'orders.center_id')
            ->where('orders.status', 'confirmed')
            ->selectRaw('centers.name AS center_name, SUM(order_items.quantity) AS total_q')
            ->groupBy('centers.name')
            ->orderByDesc('total_q')
            ->limit(5)
            ->get();

        return [
            'labels' => $rows->pluck('center_name'),
            'datasets' => [[
                'label' => 'الكمية',
                'data' => $rows->pluck('total_q'),
            ]]
        ];
    }
}

