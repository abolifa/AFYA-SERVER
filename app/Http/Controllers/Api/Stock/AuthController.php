<?php

namespace App\Http\Controllers\Api\Stock;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $validated['email'])->active()->first();


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

        if (!$user->isStockOrAdmin()) {
            return response()->json([
                'message' => 'ليس لديك صلاحية للدخول'
            ], 403);
        }
        $user->tokens()->where('name', 'stock')->delete();
        $token = $user->createToken('stock')->plainTextToken;
        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'account_type' => $user->account_type,
            ],
        ]);
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
}
