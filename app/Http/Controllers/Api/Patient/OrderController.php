<?php

namespace App\Http\Controllers\Api\Patient;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController
{
    public function index(Request $request): JsonResponse
    {
        $patient = $request->user();
        if (!$patient) {
            return response()->json(['error' => 'غير مخول'], 401);
        }

        $orders = $patient
            ->orders()
            ->with(['items.product', 'center', 'patient'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($orders);
    }

    public function show(Request $request, $orderId): JsonResponse
    {
        $patient = $request->user();
        if (!$patient) {
            return response()->json(['error' => 'غير مخول'], 401);
        }

        $order = $patient
            ->orders()
            ->with(['items.product', 'center'])
            ->find($orderId);

        if (!$order) {
            return response()->json(['error' => 'لم يتم العثور علي الطلب'], 404);
        }

        return response()->json($order);
    }

    public function store(Request $request): JsonResponse
    {
        $patient = $request->user();
        if (!$patient) {
            return response()->json(['error' => 'غير مخول'], 401);
        }
        $data = $request->validate([
            'center_id' => ['required', 'exists:centers,id'],
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $order = Model::withoutEvents(function () use ($patient, $data) {
            $order = $patient->orders()->create([
                'center_id' => $data['center_id'],
                'appointment_id' => $data['appointment_id'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            return $order->load(['items.product', 'center']);
        });

        return response()->json($order, 201);
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        $patient = $request->user();
        if (!$patient) {
            return response()->json(['error' => 'غير مخول'], 401);
        }
        $order = $patient->orders()->find($order->id);
        if (!$order) {
            return response()->json(['error' => 'لم يتم العثور علي الطلب'], 404);
        }
        if ($order->status !== 'pending') {
            return response()->json(['error' => 'لايمكن إلغاء الطلب بعد تأكيده أو إلغائه'], 400);
        }
        Model::withoutEvents(function () use ($order) {
            $order->status = 'cancelled';
            $order->save();
        });

        return response()->json(['message' => 'تم إلغاء الطلب بنجاح']);
    }

    public function update(Request $request, $orderId): JsonResponse
    {
        $patient = $request->user();
        if (!$patient) {
            return response()->json(['error' => 'غير مخول'], 401);
        }


        $order = $patient->orders()->find($orderId);
        if (!$order) {
            return response()->json(['error' => 'لم يتم العثور علي الطلب'], 404);
        }
        if ($order->status !== 'pending') {
            return response()->json(['error' => 'لايمكن تعديل الطلب بعد تأكيده أو إلغائه'], 400);
        }

        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $order = DB::transaction(function () use ($order, $data) {
            $order->items()->delete();
            foreach ($data['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
            return $order->load(['items.product', 'center']);
        });
        return response()->json($order);
    }

    public function destroy(Request $request, $orderId): JsonResponse
    {
        $patient = $request->user();
        if (!$patient) {
            return response()->json(['error' => 'غير مخول'], 401);
        }
        $order = $patient->orders()->find($orderId);
        if (!$order) {
            return response()->json(['error' => 'لم يتم العثور علي الطلب'], 404);
        }
        $order->delete();
        return response()->json(null, 204);
    }
}
