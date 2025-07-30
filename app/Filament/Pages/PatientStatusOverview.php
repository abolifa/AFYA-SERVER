<?php

namespace App\Filament\Pages;

use App\Models\Patient;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;

class PatientStatusOverview extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.patient-status-overview';
    public ?Patient $patient = null;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getSlug(): string
    {
        return 'status-overview/{patient_id}';
    }

    public function getHeading(): string
    {
        return $this->patient->name ?? 'معلومات المريض';
    }

    public function mount(int $patient_id): void
    {
        $this->patient = Patient::findOrFail($patient_id);
    }

}
