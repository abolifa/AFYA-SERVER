<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;

class PostController
{
    public function index(): JsonResponse
    {
        $posts = Post::query()
            ->where('is_published', true)
            ->oldest()
            ->paginate(6);
        return response()->json($posts);
    }

    public function show(int $id): JsonResponse
    {
        $post = Post::query()
            ->where('is_published', true)
            ->findOrFail($id);
        return response()->json($post);
    }

    public function related(int $id): JsonResponse
    {
        $post = Post::where('is_published', true)->findOrFail($id);
        $tags = collect($post->tags ?? [])
            ->map(fn($t) => trim(preg_replace('/^[#\s]+/u', '', $t))) // strip leading # and spaces
            ->filter()
            ->unique()
            ->values();

        // no tags? fallback to latest
        if ($tags->isEmpty()) {
            $fallback = Post::where('is_published', true)
                ->where('id', '!=', $post->id)
                ->latest()->limit(6)->get();
            return response()->json($fallback);
        }

        // JSON tags column
        $related = Post::where('is_published', true)
            ->where('id', '!=', $post->id)
            ->where(function ($q) use ($tags) {
                foreach ($tags as $tag) {
                    $q->orWhereJsonContains('tags', $tag)
                        ->orWhereJsonContains('tags', "#{$tag}"); // handle stored hashtags if any
                }
            })
            ->latest()->limit(6)->get();

        return response()->json($related);
    }
}
