<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Device;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class TopDevices extends ChartWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'أكثر الأجهزة استخداماً';

    protected function getData(): array
    {
        $devices = Device::all();
        $deviceUsage = $devices->map(function ($device) {
            $appointments = Appointment::where('device_id', $device->id)
                ->where('status', 'completed')
                ->get(['start_time', 'end_time']);

            $totalMinutes = $appointments->sum(function ($appointment) {
                $start = Carbon::parse($appointment->start_time);
                $end = Carbon::parse($appointment->end_time);
                return abs($end->diffInMinutes($start));
            });

            return [
                'name' => $device->name,
                'usage' => $totalMinutes,
            ];
        });

        $topDevices = $deviceUsage->sortByDesc('usage')->take(5);

        return [
            'datasets' => [
                [
                    'label' => 'الاستخدام (دقائق)',
                    'data' => $topDevices->pluck('usage')->values(),
                    'backgroundColor' => [
                        '#FF6384', // Pink
                        '#36A2EB', // Blue
                        '#FFCE56', // Yellow
                        '#4BC0C0', // Teal
                        '#9966FF', // Purple
                    ],
                ],
            ],
            'labels' => $topDevices->pluck('name')->values(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
