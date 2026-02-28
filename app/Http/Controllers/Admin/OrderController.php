<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\ShippingService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;
    protected ShippingService $shippingService;

    public function __construct(OrderService $orderService, ShippingService $shippingService)
    {
        $this->orderService = $orderService;
        $this->shippingService = $shippingService;
    }

    public function index(Request $request)
    {
        $query = Order::with(['user', 'shipment']);

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(int $id)
    {
        $order = Order::with(['user', 'items.variant.product', 'shipment', 'payment'])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        if ($request->status === 'cancelled' && !$order->canBeCancelled()) {
            return back()->withErrors(['error' => 'This order cannot be cancelled.']);
        }

        if ($request->status === 'cancelled') {
            $reason = $request->cancellation_reason ?? 'Cancelled by admin';
            $this->orderService->cancelOrder($order, $reason);
            return back()->with('success', 'Order cancelled successfully.');
        }

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated successfully.');
    }

    public function updateShipment(Request $request, int $id)
    {
        $order = Order::with('shipment')->findOrFail($id);

        $request->validate([
            'tracking_number' => 'required|string|max:100',
            'courier_name' => 'nullable|string|max:100',
        ]);

        $shipment = $order->shipment;

        if (!$shipment) {
            $shipment = $this->shippingService->createShipmentForOrder($order);
        }

        $this->shippingService->addTrackingNumber(
            $shipment,
            $request->tracking_number
        );

        if ($request->courier_name) {
            $shipment->update(['courier_name' => $request->courier_name]);
        }

        return back()->with('success', 'Shipment updated successfully.');
    }
}
