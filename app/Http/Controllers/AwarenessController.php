<?php

namespace App\Http\Controllers;

use App\Models\Awareness;
use Illuminate\Http\JsonResponse;

class AwarenessController
{
    public function index(): JsonResponse
    {
        $awareness = Awareness::all()->select('id', 'title');
        return response()->json($awareness);
    }

    public function show($id): JsonResponse
    {
        $awareness = Awareness::findOrFail($id);
        return response()->json($awareness);
    }
}
