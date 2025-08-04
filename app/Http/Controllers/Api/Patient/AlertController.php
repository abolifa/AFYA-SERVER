<?php

namespace App\Http\Controllers\Api\Patient;

use App\Models\Alert;
use App\Models\Appointment;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlertController
{

    public function getNotifications(Request $request): JsonResponse
    {
        $patient = $request->user();

        if (!$patient) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $now = Carbon::now();

        $appointments = Appointment::where('patient_id', $patient->id)
            ->where(function ($query) use ($now) {
                $query->whereDate('date', '>', $now->toDateString())
                    ->orWhere(function ($q) use ($now) {
                        $q->whereDate('date', $now->toDateString())
                            ->whereTime('time', '>=', $now->toTimeString());
                    });
            })
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $notifications = $appointments->map(function ($appt) use ($now) {
            $apptDateTime = Carbon::parse($appt->date . ' ' . $appt->time);
            return [
                'id' => $appt->id,
                'type' => 'appointment_upcoming',
                'message' => 'لديك موعد قادم',
                'date' => $appt->date,
                'time' => $appt->time,
                'human_time' => $apptDateTime->diffForHumans($now, [
                    'syntax' => CarbonInterface::DIFF_RELATIVE_TO_NOW,
                    'parts' => 1,
                ]),
                'in_hours' => $now->diffInHours($apptDateTime, false),
            ];
        });

        return response()->json($notifications);
    }

    /**
     * List all stored alerts.
     */
    public function index(Request $request): JsonResponse
    {
        $patient = $request->user();

        if (!$patient) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $alerts = Alert::where('patient_id', $patient->id)
            ->orderBy('is_read', 'asc')         // Unread first (is_read = 0)
            ->orderBy('created_at', 'desc')     // Newest first
            ->get();
        return response()->json($alerts);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $patient = $request->user();
        $alert = Alert::where('id', $id)
            ->where('patient_id', $patient->id)
            ->firstOrFail();

        return response()->json($alert);
    }

    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $patient = $request->user();

        $alert = Alert::where('id', $id)
            ->where('patient_id', $patient->id)
            ->firstOrFail();

        $alert->update(['is_read' => true]);

        return response()->json(['message' => 'Alert marked as read']);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $patient = $request->user();

        Alert::where('patient_id', $patient->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['message' => 'All alerts marked as read']);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $patient = $request->user();

        $alert = Alert::where('id', $id)
            ->where('patient_id', $patient->id)
            ->firstOrFail();

        $alert->delete();

        return response()->json(['message' => 'Alert deleted']);
    }
}
