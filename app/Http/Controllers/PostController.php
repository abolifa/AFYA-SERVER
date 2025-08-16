<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;

class PostController
{
    public function index(): JsonResponse
    {
        $posts = Post::query()
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(6);
        return response()->json($posts);
    }

    public function show(string $slug): JsonResponse
    {
        $post = Post::query()
            ->where('slug', $slug)
            ->where('active', true)
            ->firstOrFail();
        return response()->json($post);
    }


    public function related(string $slug): JsonResponse
    {
        $post = Post::query()
            ->where('slug', $slug)
            ->where('active', true)
            ->firstOrFail();
        $tags = array_values(array_filter((array)($post->tags ?? [])));
        if (empty($tags)) {
            $fallback = Post::query()
                ->where('active', true)
                ->whereKeyNot($post->getKey())
                ->latest()
                ->limit(3)
                ->get();
            return response()->json($fallback);
        }
        $sumExprs = [];
        $bindings = [];
        foreach ($tags as $t) {
            $sumExprs[] = "JSON_CONTAINS(`tags`, JSON_QUOTE(?), '$')";
            $bindings[] = $t;
        }
        $scoreSql = implode(' + ', $sumExprs);
        $related = Post::query()
            ->select('*')
            ->selectRaw("($scoreSql) as match_score", $bindings)
            ->where('active', true)
            ->whereKeyNot($post->getKey())
            ->where(function ($q) use ($tags) {
                foreach ($tags as $t) {
                    $q->orWhereJsonContains('tags', $t);
                }
            })
            ->orderByDesc('match_score')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
        return response()->json($related);
    }
}
