<?php

namespace App\Observers;

use App\Models\Alert;
use App\Models\Order;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;

class OrderObserver
{
    public function saved(Order $order): void
    {
        if ($order->wasRecentlyCreated) {
            $exists = Alert::where('type', 'order')
                ->where('type_id', $order->id)
                ->where('message', 'تم إنشاء طلب جديد')
                ->exists();

            if (!$exists && $order->patient_id) {
                Alert::create([
                    'patient_id' => $order->patient_id,
                    'type' => 'order',
                    'type_id' => $order->id,
                    'message' => 'تم إنشاء طلب جديد',
                ]);
            }
        }
    }

    public function updated(Order $order): void
    {
        // 🔔 Create alert on order status update
        $statusArabic = [
            'pending' => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'cancelled' => 'ملغى',
            'completed' => 'مكتمل',
        ];

        if ($order->isDirty('status')) {
            Alert::create([
                'patient_id' => $order->patient_id,
                'type' => 'order',
                'type_id' => $order->id,
                'message' => 'تم تحديث حالة الطلب إلى: ' . ($statusArabic[$order->status] ?? $order->status),
            ]);
        }

        // 📦 Stock movement handling (moved from StockMovementObserver)
        $from = $order->getOriginal('status');
        $to = $order->status;

        // only on first time confirming
        if ($from !== 'confirmed' && $to === 'confirmed') {
            $movement = StockMovement::create([
                'type' => 'out',
                'actor_type' => Auth::check() ? Auth::user()::class : null,
                'actor_id' => Auth::id(),
                'subject_type' => Order::class,
                'subject_id' => $order->id,
                'from_center_id' => $order->center_id,
                'to_center_id' => null,
                'patient_id' => $order->patient_id,
                'supplier_id' => null,
            ]);

            foreach ($order->items as $item) {
                $movement->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                ]);
            }
        }
    }
}
