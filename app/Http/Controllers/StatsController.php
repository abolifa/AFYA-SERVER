<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Center;
use App\Models\Order;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class StatsController
{
    public function index(): JsonResponse
    {
        $centers = Center::count();
        $users = User::count();
        $patients = Patient::count();
        $appointments = Appointment::count();
        $doctors = User::where('is_doctor', true)->count();
        $orders = Order::count();

        return response()->json([
            'centers' => $centers,
            'users' => $users,
            'patients' => $patients,
            'appointments' => $appointments,
            'doctors' => $doctors,
            'orders' => $orders,
        ]);
    }
}
