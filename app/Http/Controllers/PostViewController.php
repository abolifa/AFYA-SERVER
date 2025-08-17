<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostViewController
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'slug' => 'required|string|max:255',
            'uid' => 'nullable|string|max:64',
        ]);

        $ip = $request->ip();
        $ua = substr((string)$request->userAgent(), 0, 200);
        $today = Carbon::today();
        $raw = ($data['uid'] ?? '') . '|' . $ip . '|' . $ua . '|' . $today->toDateString();
        $visitorHash = hash('sha256', $raw);
        $exists = DB::table('post_views')->where([
            'slug' => $data['slug'],
            'visitor_hash' => $visitorHash,
            'view_date' => $today->toDateString(),
        ])->first();

        if ($exists) {
            //
        } else {
            DB::table('post_views')->insert([
                'slug' => $data['slug'],
                'visitor_hash' => $visitorHash,
                'view_date' => $today->toDateString(),
                'views' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return response()->json(['ok' => true]);
    }
}
