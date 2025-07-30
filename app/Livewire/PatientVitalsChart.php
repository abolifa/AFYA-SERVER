<?php

namespace App\Livewire;

use App\Models\Vital;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PatientVitalsChart extends ChartWidget
{
    protected static ?string $heading = 'الرسم البياني للمؤشرات الحيوية';
    protected static ?string $maxHeight = '350px';
    public ?int $patientId = null;

    protected function getData(): array
    {
        if (!$this->patientId) {
            return ['labels' => [], 'datasets' => []];
        }

        $vitals = Vital::where('patient_id', $this->patientId)
            ->orderBy('recorded_at')
            ->take(30)
            ->get(['recorded_at', 'weight', 'systolic', 'diastolic', 'heart_rate', 'temperature', 'oxygen_saturation', 'sugar_level']);

        $labels = $vitals->pluck('recorded_at')->map(fn($date) => Carbon::parse($date)->format('d/m/Y'))->toArray();

        return [
            'labels' => $labels,
            'datasets' => array_filter([
                $this->buildDataset('الوزن (كجم)', $vitals->pluck('weight')->toArray(), 'rgb(75, 192, 192)'),
                $this->buildDataset('ضغط الدم (Systolic)', $vitals->pluck('systolic')->toArray(), 'rgb(255, 99, 132)'),
                $this->buildDataset('ضغط الدم (Diastolic)', $vitals->pluck('diastolic')->toArray(), 'rgb(255, 159, 64)'),
                $this->buildDataset('معدل ضربات القلب', $vitals->pluck('heart_rate')->toArray(), 'rgb(54, 162, 235)'),
                $this->buildDataset('درجة الحرارة', $vitals->pluck('temperature')->toArray(), 'rgb(153, 102, 255)'),
                $this->buildDataset('نسبة الأكسجين', $vitals->pluck('oxygen_saturation')->toArray(), 'rgb(201, 203, 207)'),
                $this->buildDataset('مستوى السكر', $vitals->pluck('sugar_level')->toArray(), 'rgb(255, 205, 86)'),
            ]),
        ];
    }

    private function buildDataset(string $label, array $data, string $color): ?array
    {
        if (empty(array_filter($data))) return null;

        return [
            'label' => $label,
            'data' => $data,
            'borderColor' => $color,
            'backgroundColor' => $color,
            'tension' => 0.3, // Smooth line
            'fill' => false,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
