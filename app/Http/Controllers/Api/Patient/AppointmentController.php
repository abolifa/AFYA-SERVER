<?php

namespace App\Http\Controllers\Api\Patient;

use App\Models\Scopes\ByCenterScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppointmentController
{
    public function index(Request $request): JsonResponse
    {
        $patient = $request->user();
        $appointments = $patient
            ->appointments()
            ->with(['center', 'doctor'])
            ->withoutGlobalScope(ByCenterScope::class)
            ->orderBy('date', 'desc')
            ->paginate(10);
        return response()->json($appointments);
    }

    public function getAppointmentsId(Request $request): JsonResponse
    {
        $patient = $request->user();
        $appointments = $patient
            ->appointments()
            ->withoutGlobalScope(ByCenterScope::class)
            ->orderBy('id', 'desc')
            ->select('id', 'status', 'date', 'time')
            ->get();

        return response()->json($appointments);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'center_id' => ['required', 'exists:centers,id'],
            'doctor_id' => ['nullable', 'exists:users,id'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i:s'],
            'notes' => ['nullable', 'string'],
        ]);

        $appointment = $request->user()->appointments()->create([
            ...$data,
            'patient_id' => $request->user()->id,
            'device_id' => auth()->user()->device_id ?? null,
            'status' => 'pending',
            'intended' => false,
        ]);

        return response()->json([
            'message' => 'تم حجز الموعد بنجاح',
            'appointment' => $appointment->load('center', 'doctor'),
        ], 201);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $appointment = $request->user()->appointments()
            ->with(['center', 'doctor'])
            ->withoutGlobalScope(ByCenterScope::class)
            ->findOrFail($id);

        return response()->json($appointment);
    }

    public function reschedule(Request $request, $id): JsonResponse
    {
        $appointment = $request->user()->appointments()->findOrFail($id);

        $data = $request->validate([
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i:s'],
        ]);

        $appointment->update([
            'date' => $data['date'],
            'time' => $data['time'],
        ]);

        return response()->json([
            'message' => 'تم إعادة جدولة الموعد',
            'appointment' => $appointment->load('center', 'doctor'),
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $appointment = $request->user()->appointments()->findOrFail($id);

        $data = $request->validate([
            'center_id' => ['nullable', 'exists:centers,id'],
            'doctor_id' => ['nullable', 'exists:users,id'],
            'device_id' => ['nullable', 'exists:devices,id'],
            'date' => ['nullable', 'date'],
            'time' => ['nullable', 'date_format:H:i:s'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['pending', 'confirmed', 'cancelled', 'completed'])],
        ]);

        $appointment->update([
            ...$data,
        ]);

        return response()->json([
            'message' => 'تم تحديث بيانات الموعد',
            'appointment' => $appointment->load('center', 'doctor'),
        ]);
    }

    public function cancel(Request $request, $id): JsonResponse
    {
        $appointment = $request->user()->appointments()->findOrFail($id);

        $appointment->update([
            'status' => 'cancelled',
        ]);

        return response()->json([
            'message' => 'تم إلغاء الموعد',
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $appointment = $request->user()->appointments()->findOrFail($id);

        $appointment->delete();

        return response()->json([
            'message' => 'تم حذف الموعد بنجاح',
        ]);
    }
}
