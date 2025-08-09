<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Center;
use App\Models\Device;
use App\Models\Order;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteStatisticController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $center = Center::count();
        $patients = Patient::count();
        $appointments = Appointment::count();
        $orders = Order::count();
        $prescriptions = Prescription::count();
        $devices = Device::count();
        $doctors = User::where('is_doctor', true)->count();

        return response()->json([
            'centers' => $center,
            'patients' => $patients,
            'appointments' => $appointments,
            'orders' => $orders,
            'devices' => $devices,
            'doctors' => $doctors,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
