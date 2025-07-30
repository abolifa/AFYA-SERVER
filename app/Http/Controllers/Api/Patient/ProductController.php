<?php

namespace App\Http\Controllers\Api\Patient;

use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController
{
    public function index(): JsonResponse
    {
        $products = Product::all();
        return response()->json($products);
    }
}
