<?php

namespace App\Http\Controllers\Flutter;

use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Nette\Utils\Random;

class AuthController
{
    public function checkNationalId(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'national_id' => 'required|string|max:12|min:12',
        ]);
        $exists = Patient::where('national_id', $validated['national_id'])->exists();
        return response()->json(['exists' => $exists]);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:10|min:10|regex:/^0[0-9]{9}$/',
            'password' => 'required|string|min:6|max:20',
        ]);
        $patient = Patient::where('phone', $validated['phone'])->first();
        if (!$patient) {
            return response()->json(['message' => 'المستخدم غير موجود'], 404);
        }
        if (!password_verify($validated['password'], $patient->password)) {
            return response()->json(['message' => 'كلمة المرور غير صحيحة'], 401);
        }
        $token = $patient->createToken('auth_token')->plainTextToken;
        return response()->json([
            'token' => $token,
            'patient' => $patient->load(['center', 'device']),
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'national_id' => 'required|string|max:12|min:12|unique:patients,national_id',
            'phone' => 'required|string|max:10|min:10|regex:/^0[0-9]{9}$/|unique:patients,phone',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|max:20|confirmed',
            'password_confirmation' => 'required|string|min:6|max:20',
        ], [
            'national_id.unique' => 'الرقم الوطني مسجل مسبقاً',
            'national_id.required' => 'الرقم الوطني مطلوب',
            'phone.unique' => 'رقم الهاتف مسجل مسبقاً',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.regex' => 'رقم الهاتف يجب أن يبدأ بـ 0 ويتكون من 10 أرقام',
            'name.required' => 'الاسم مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        $patient = Patient::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'national_id' => $validated['national_id'],
            'password' => $validated['password'],
            'file_number' => Random::generate(10, '0-9'),
            'verified' => false,
        ]);

        $token = $patient->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'patient' => $patient->load(['center', 'device']),
        ]);
    }


    public function me(Request $request): JsonResponse
    {
        $patient = $request->user();
        if (!$patient) {
            return response()->json(['message' => 'المستخدم غير موجود'], 404);
        }
        return response()->json($patient);
    }
}
