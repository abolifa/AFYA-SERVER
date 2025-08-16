<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\JsonResponse;

class AnnouncementController
{
    public function index(): JsonResponse
    {
        $announcements = Announcement::query()
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($announcements);
    }

    public function show(Announcement $announcement): JsonResponse
    {
        return response()->json($announcement);
    }
}
