<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class ClockWidget extends Widget
{
    protected static string $view = 'filament.widgets.clock-widget';
    protected static ?int $sort = -1; // optional: position at top
    protected static bool $isLazy = false;
    public ?string $time = null;
    protected int|string|array $columnSpan = 1;

    public function mount(): void
    {
        $this->updateTime();
    }

    public function updateTime(): void
    {
        $this->time = Carbon::now()->format('h:i A');
    }

    protected function getViewData(): array
    {
        return [
            'time' => $this->time,
        ];
    } // render immediately
}
