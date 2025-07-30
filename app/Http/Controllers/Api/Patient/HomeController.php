<?php

namespace App\Http\Controllers\Api\Patient;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController
{
    public function index(Request $request): JsonResponse
    {
        $patient = $request->user();

        $appointments = $patient->appointments()->with('doctor', 'center', 'order.items')
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        $orders = $patient->orders()->with('items.product', 'center')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'appointments' => $appointments,
            'orders' => $orders,
        ]);
    }
}
