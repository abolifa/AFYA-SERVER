<?php

namespace App\Observers;

use App\Models\Alert;
use App\Models\Prescription;

class PrescriptionObserver
{
    /**
     * Handle the Prescription "created" event.
     */
    public function created(Prescription $prescription): void
    {
        Alert::create([
            'patient_id' => $prescription->patient_id,
            'type' => 'prescription',
            'type_id' => $prescription->id,
            'message' => 'تم إنشاء وصفة جديدة',
        ]);
    }

    public function updated(Prescription $prescription): void
    {
        Alert::create([
            'patient_id' => $prescription->patient_id,
            'type' => 'prescription',
            'type_id' => $prescription->id,
            'message' => 'تم تحديث الوصفة',
        ]);
    }

    /**
     * Handle the Prescription "deleted" event.
     */
    public function deleted(Prescription $prescription): void
    {
        //
    }

    /**
     * Handle the Prescription "restored" event.
     */
    public function restored(Prescription $prescription): void
    {
        //
    }

    /**
     * Handle the Prescription "force deleted" event.
     */
    public function forceDeleted(Prescription $prescription): void
    {
        //
    }
}
