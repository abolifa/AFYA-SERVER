<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComplaintController
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:1000',
        ]);
        $complaint = Complaint::create($data);
        return response()->json(['message' => 'Complaint submitted successfully', 'complaint' => $complaint], 201);
    }
}
