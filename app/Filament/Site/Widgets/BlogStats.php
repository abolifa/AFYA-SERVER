<?php

namespace App\Filament\Site\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class BlogStats extends BaseWidget
{
    protected function getStats(): array
    {
        $total = DB::table('post_views')->sum('views');
        $today = DB::table('post_views')->whereDate('view_date', today())->sum('views');
        $week = DB::table('post_views')->whereBetween('view_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('views');
        return [
            Stat::make('إجمالي الزيارات', number_format($total))
                ->color('success')
                ->description('كل الزيارات')
                ->icon('fas-chart-line'),
            Stat::make('زيارات اليوم', number_format($today))
                ->color('info')
                ->description('زيارات اليوم فقط')
                ->icon('fas-chart-line'),
            Stat::make('زيارات هذا الأسبوع', number_format($week))
                ->color('danger')
                ->description('زيارات الأسبوع الحالي')
                ->icon('fas-chart-line'),
        ];
    }
}
