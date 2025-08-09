<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\JsonResponse;

class SliderController
{
    public function index(): JsonResponse
    {
        $sliders = Slider::query()
            ->where('active', true)
            ->get();
        return response()->json($sliders);
    }
}
