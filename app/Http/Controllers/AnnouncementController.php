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
            ->select('id', 'title')
            ->get();
        return response()->json($announcements);
    }

    public function show(int $id): JsonResponse
    {
        $announcement = Announcement::query()
            ->where('active', true)
            ->findOrFail($id);

        return response()->json($announcement);
    }
}
