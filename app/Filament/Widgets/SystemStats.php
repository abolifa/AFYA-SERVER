<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Center;
use App\Models\Order;
use App\Models\Patient;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class SystemStats extends BaseWidget
{
    use HasWidgetShield;

    protected function getStats(): array
    {
        $counts = Cache::remember(
            'dashboard_stats_static',
            now()->addMinutes(30),
            fn() => [
                'centers' => Center::count(),
                'doctors' => User::where('is_doctor', true)->count(),
                'patients' => Patient::count(),
            ],
        );

        $todayAppts = Appointment::whereDate('date', today())->count();

        $todayOrders = Order::whereDate('created_at', today())
            ->where('status', 'confirmed')
            ->count();


        $todayLabel = Carbon::now()
            ->locale('ar')
            ->translatedFormat('l d/m/Y');

        return [
            Stat::make('المراكز', $counts['centers'])
                ->description('عدد المراكز الصحية المسجلة')
                ->url(
                    url('admin/centers'),
                ),
            Stat::make('الأطباء', $counts['doctors'])
                ->description('عدد الأطباء المسجلين')
                ->url(
                    url('admin/users'),
                ),
            Stat::make('المرضى', $counts['patients'])
                ->description('عدد المرضى المسجلين')
                ->url(
                    url('admin/patients'),
                ),

            Stat::make('إجمالي المنتجات', Order::all()->count())
                ->url(
                    url('admin/products'),
                )
                ->color('warning')
                ->description('إجمالي عدد المنتجات المسجلة'),

            Stat::make('طلبات اليوم', $todayOrders)
                ->url(
                    url('admin/orders'),
                )
                ->color('primary')
                ->description('طلبات المرضى اليوم'),


            Stat::make('مواعيد اليوم', $todayAppts)
                ->url(
                    url('admin/appointments'),
                )
                ->color('success')
                ->description($todayLabel),
        ];
    }
}
