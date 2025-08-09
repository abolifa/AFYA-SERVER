<?php

namespace App\Http\Controllers\Api\Patient;

use App\Models\Scopes\ByCenterScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController
{
    public function index(Request $request): JsonResponse
    {
        $patient = $request->user();
        $appointments_count = $patient->appointments()->withoutGlobalScope(ByCenterScope::class)->count();
        $orders_count = $patient->orders()->count();
        $prescriptions_count = $patient->prescriptions()->count();
        $appointments = $patient->appointments()->withoutGlobalScope(ByCenterScope::class)->with('center', 'doctor')->latest()->take(5)->get();
        $orders = $patient->orders()->latest()->take(5)->get();
        $prescriptions = $patient->prescriptions()->latest()->take(5)->get();

        return response()->json([
            'appointments_count' => $appointments_count,
            'orders_count' => $orders_count,
            'prescriptions_count' => $prescriptions_count,
            'appointments' => $appointments,
            'orders' => $orders,
            'prescriptions' => $prescriptions,
        ]);
    }
}
