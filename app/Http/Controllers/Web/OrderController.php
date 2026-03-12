<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\OrderStatusMapper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function show(int $id)
    {
        $order = Auth::user()->orders()
            ->with(['items.variant.product', 'shipment', 'payment'])
            ->findOrFail($id);

        return view('web.orders.show', compact('order'));
    }

    public function cancel(Request $request, int $id)
    {
        $order = Auth::user()->orders()->findOrFail($id);

        if (!$order->canBeCancelled()) {
            return back()->withErrors(['cancel' => 'Bu sipariş iptal edilemez.']);
        }

        $order->update([
            'status' => 'cancelled',
            'fulfillment_status' => OrderStatusMapper::FULFILLMENT_CANCELLED,
            'payment_status' => $order->isPaid() ? OrderStatusMapper::PAYMENT_CANCELLED : OrderStatusMapper::PAYMENT_PENDING,
            'status_updated_at' => now(),
            'cancelled_at' => now(),
            'cancellation_reason' => $request->reason ?? 'Müşteri tarafından iptal edildi',
        ]);

        return redirect()->route('profile.index', ['tab' => 'orders'])->with('success', 'Siparişiniz iptal edildi.');
    }
}
