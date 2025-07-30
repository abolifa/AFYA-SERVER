<?php

namespace App\Http\Controllers\Api\Stock;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int)$request->query('per_page', 10);
        if ($perPage > 100) $perPage = 100;

        $query = Product::query();

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }
        if ($search = $request->query('q')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->boolean('with_trashed')) {
            $query->withTrashed();
        } elseif ($request->boolean('only_trashed')) {
            $query->onlyTrashed();
        }

        $products = $query
            ->latest()
            ->paginate($perPage);

        return response()->json($products);
    }

    public function trashed(Request $request): JsonResponse
    {
        $perPage = (int)$request->query('per_page', 10);
        if ($perPage > 100) $perPage = 100;

        $products = Product::onlyTrashed()
            ->latest('deleted_at')
            ->paginate($perPage);

        return response()->json($products);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['medicine', 'equipment', 'service', 'other'])],
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:500'],
            'expiry_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'usage' => ['nullable', 'string'],
            'alert_threshold' => ['required', 'integer', 'min:0'],
        ], [
            'name.required' => 'اسم المنتج مطلوب',
        ]);

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['sometimes', Rule::in(['medicine', 'equipment', 'service', 'other'])],
            'name' => ['sometimes', 'string', 'max:255'],
            'image' => ['sometimes', 'nullable', 'string', 'max:500'],
            'expiry_date' => ['sometimes', 'nullable', 'date'],
            'description' => ['sometimes', 'nullable', 'string'],
            'usage' => ['sometimes', 'nullable', 'string'],
            'alert_threshold' => ['sometimes', 'integer', 'min:0'],
        ]);

        $product->update($validated);

        return response()->json($product);
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $product->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function restore(int $id): JsonResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        return response()->json(['message' => 'Restored', 'data' => $product]);
    }

    public function forceDelete(int $id): JsonResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->forceDelete();
        return response()->json(['message' => 'Force deleted']);
    }
}
