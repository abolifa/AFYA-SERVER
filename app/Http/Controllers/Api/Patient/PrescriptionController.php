<?php

namespace App\Http\Controllers\Api\Patient;

use App\Models\Prescription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrescriptionController
{
    public function index(Request $request): JsonResponse
    {
        $patient = $request->user();
        $prescriptions = Prescription::with('doctor')->where('patient_id', $patient->id)
            ->orderBy('date', 'desc')
            ->select([
                'id',
                'date',
                'doctor_id',
            ])
            ->paginate(10);
        return response()->json($prescriptions);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $appointment = $request->user()->prescriptions()
            ->with(['doctor', 'items.product'])
            ->findOrFail($id);
        return response()->json($appointment);
    }

}
