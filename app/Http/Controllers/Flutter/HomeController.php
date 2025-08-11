<?php

namespace App\Http\Controllers\Flutter;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController
{
    public function index(Request $request): JsonResponse
    {
        $patient = $request->user();
        if (!$patient) {
            return response()->json(['message' => 'غير مصرح'], 401);
        }
        $appointments = $patient->appointments()
            ->with(['doctor', 'center'])
            ->latest()
            ->get();
        $orders = $patient->orders()->with('items')
            ->latest()
            ->get();
        $prescriptions = $patient->prescriptions()
            ->latest()
            ->get();

        return response()->json([
            'appointments' => $appointments,
            'orders' => $orders,
            'prescriptions' => $prescriptions,
        ]);
    }
}
