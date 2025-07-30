<?php

namespace App\Http\Controllers\Api\Patient;

use App\Models\Center;
use Illuminate\Http\JsonResponse;

class CenterController
{
    public function index(): JsonResponse
    {
        $products = Center::all();
        return response()->json($products);
    }
}
