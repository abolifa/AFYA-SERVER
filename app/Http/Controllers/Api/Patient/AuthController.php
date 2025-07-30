<?php

namespace App\Http\Controllers\Api\Patient;

use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthController
{

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => [
                'required', 'string', 'size:10', 'starts_with:091,092,093,094,095', 'regex:/^[0-9]+$/'
            ],
            'password' => 'required|string|min:6',
        ]);

        $user = Patient::where('phone', $validated['phone'])->first();


        if (!$user) {
            return response()->json([
                'message' => 'لم يتم العثور علي المستخدم'
            ], 404);
        }

        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'كلمة المرور غير صحيحة'
            ], 401);
        }

        $user->tokens()->where('name', 'patient')->delete();
        $token = $user->createToken('patient')->plainTextToken;
        return response()->json([
            'token' => $token,
        ]);
    }


    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'national_id' => ['required', 'string', 'size:12', 'regex:/^\d{12}$/', 'unique:patients,national_id'],
            'family_issue_number' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required', 'string', 'size:10',
                'starts_with:091,092,093,094,095',
                'regex:/^[0-9]+$/',
                'unique:patients,phone',
            ],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'email' => ['nullable', 'email', 'max:255', 'unique:patients,email'],
            'gender' => ['nullable', 'in:male,female'],
            'dob' => ['nullable', 'date'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'image' => ['nullable', 'string'],
            'center_id' => ['nullable', 'exists:centers,id'],
        ]);

        do {
            $fileNumber = Str::upper(Str::random(6));
        } while (Patient::where('file_number', $fileNumber)->exists());

        $validated['file_number'] = $fileNumber;

        unset($validated['password_confirmation']);

        $patient = Patient::create([
            'national_id' => $validated['national_id'],
            'family_issue_number' => $validated['family_issue_number'] ?? null,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => bcrypt($validated['password']),
            'email' => $validated['email'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'dob' => $validated['dob'] ?? null,
            'blood_group' => $validated['blood_group'] ?? null,
            'image' => $validated['image'] ?? null,
            'center_id' => $validated['center_id'] ?? null,
            'file_number' => $validated['file_number'],
        ]);

        $token = $patient->createToken('patient')->plainTextToken;

        return response()->json([
            'token' => $token,
            'message' => 'تم إنشاء الحساب بنجاح',
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'تم تسجيل الخروج']);
    }


    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }


    public function update(Request $request): JsonResponse
    {
        $patient = $request->user();
        $data = $request->validate([
            'national_id' => [
                'nullable', 'string', 'size:12', 'regex:/^\d{12}$/',
                Rule::unique('patients', 'national_id')->ignore($patient->id),
            ],
            'family_issue_number' => ['nullable', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => [
                'nullable', 'string', 'size:10',
                'starts_with:091,092,093,094,095',
                'regex:/^[0-9]+$/',
                Rule::unique('patients', 'phone')->ignore($patient->id),
            ],
            'email' => [
                'nullable', 'email', 'max:255',
                Rule::unique('patients', 'email')->ignore($patient->id),
            ],
            'gender' => ['nullable', 'in:male,female'],
            'dob' => ['nullable', 'date'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'center_id' => ['nullable', 'exists:centers,id'],
        ]);

        if (!empty($data['dob'])) {
            $data['dob'] = Carbon::parse($data['dob']);
        }

        $patient->fill($data);
        $patient->save();
        return response()->json([
            'message' => 'تم تحديث البيانات بنجاح',
            'user' => $patient,
        ]);
    }


    public function uploadImage(Request $request): JsonResponse
    {
        $patient = $request->user();
        if (!$patient) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $request->validate([
            'image' => ['required', 'image', 'max:2048'],
        ]);
        if ($patient->image) {
            Storage::disk('public')->delete($patient->image);
        }
        $path = $request->file('image')->store('patients', 'public');
        $patient->image = $path;
        $patient->save();
        return response()->json([
            'message' => 'تم رفع الصورة بنجاح',
            'image_url' => Storage::disk('public')->url($path),
        ], 201);
    }
}
