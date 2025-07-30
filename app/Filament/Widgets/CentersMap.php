<?php

namespace App\Filament\Widgets;

use App\Models\Center;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Widget;

class CentersMap extends Widget
{
    use HasWidgetShield;

    protected static string $view = 'filament.widgets.centers-map';

    // Make it span full width in grid
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'centers' => $this->getCentersData(),
        ];
    }

    protected function getCentersData(): array
    {
        return Center::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'phone' => $c->phone,
                'address' => $c->address,
                'lat' => (float)$c->latitude,
                'lng' => (float)$c->longitude,
            ])
            ->values()
            ->toArray();
    }
}
