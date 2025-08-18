<?php

namespace App\Http\Controllers\Api\Patient;

use App\Models\Center;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CenterController
{
    public function index(): JsonResponse
    {
        $centers = Center::with(['doctors' => function ($query) {
            $query->select('id', 'name', 'phone', 'center_id');
        }, 'schedules' => function ($query) {
            $query->select('id', 'center_id', 'day', 'start_time', 'end_time', 'is_active');
        }])
            ->get();
        return response()->json($centers);
    }

    public function getDoctors(Center $center): JsonResponse
    {
        $doctors = $center->doctors()->select('id', 'name', 'phone')->get();
        return response()->json($doctors);
    }

    public function getSchedule(Center $center): JsonResponse
    {
        $schedule = $center->schedules()->get();
        return response()->json($schedule);
    }


    public function getCenters(Request $request): JsonResponse
    {
        $centers = Center::query()->select('id', 'name', 'phone', 'email')->get();
        return response()->json($centers);
    }

    public function guestCenters(): JsonResponse
    {
        $centers = Center::with('doctors', 'schedules')->get();
        return response()->json($centers);
    }


    public function checkCenter(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $center = Center::query()
            ->where('name', $request->name)
            ->first();

        if ($center) {
            return response()->json([
                'exists' => true,
                'center' => $center,
            ]);

        } else {
            return response()->json([
                'exists' => false,
                'message' => 'لم يتم العثور على المركز',
            ], 404);
        }
    }
}
