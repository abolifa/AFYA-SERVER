<?php

namespace App\Observers;

use App\Models\Alert;
use App\Models\Appointment;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        Alert::create([
            'patient_id' => $appointment->patient_id,
            'type' => 'appointment',
            'type_id' => $appointment->id,
            'message' => 'تم حجز موعد جديد',
        ]);
    }

    public function updated(Appointment $appointment): void
    {
        $statusArabic = [
            'pending' => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'cancelled' => 'ملغى',
            'completed' => 'مكتمل',
        ];
        $message = 'تم تحديث حالة الموعد إلى: ' . ($statusArabic[$appointment->status] ?? $appointment->status);
        Alert::create([
            'patient_id' => $appointment->patient_id,
            'type' => 'appointment',
            'type_id' => $appointment->id,
            'message' => $message,
        ]);
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "restored" event.
     */
    public function restored(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "force deleted" event.
     */
    public function forceDeleted(Appointment $appointment): void
    {
        //
    }
}
