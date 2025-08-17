<?php

namespace App\Http\Controllers;

use App\Models\Structure;
use Illuminate\Http\JsonResponse;

class StructureController
{
    public function index(): JsonResponse
    {
        $structures = Structure::all();
        return response()->json($structures);
    }
}
