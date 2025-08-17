<?php

namespace App\Http\Controllers;

use App\Models\Awareness;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class AwarenessController
{
    public function index(): JsonResponse
    {
        $awareness = Awareness::query()
            ->select(['id', 'title'])
            ->get()
            ->map(function ($item) {
                $item->slug = Str::slug($item->title);
                return $item;
            });
        return response()->json($awareness);
    }

    public function show(int $id): JsonResponse
    {
        $awareness = Awareness::query()
            ->findOrFail($id);
        return response()->json($awareness);
    }
}
