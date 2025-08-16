<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\JsonResponse;

class SliderController
{
    public function index(): JsonResponse
    {
        $sliders = Slider::where('active', true)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($sliders);
    }
}
