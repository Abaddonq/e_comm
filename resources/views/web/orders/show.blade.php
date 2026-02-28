@extends('layouts.web')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('orders.index') }}" class="text-indigo-600 hover:text-indigo-800">
            &larr; Back to Orders
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
                <p class="text-sm text-gray-500">Placed on {{ $order->created_at->format('M d, Y') }}</p>
            </div>
            @php
                $statusClasses = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'processing' => 'bg-blue-100 text-blue-800',
                    'shipped' => 'bg-purple-100 text-purple-800',
                    'delivered' => 'bg-green-100 text-green-800',
                    'cancelled' => 'bg-red-100 text-red-800',
                    'payment_failed' => 'bg-red-100 text-red-800',
                ];
            @endphp
            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h2>
                <div class="divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <div class="py-4 flex">
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-gray-900">{{ $item->product_title }}</h3>
                                @if($item->variant_sku)
                                    <p class="text-xs text-gray-500">SKU: {{ $item->variant_sku }}</p>
                                @endif
                                @if($item->variant_attributes)
                                    <p class="text-xs text-gray-500">
                                        @foreach(json_decode($item->variant_attributes, true) ?? [] as $key => $value)
                                            {{ ucfirst($key) }}: {{ $value }}@if(!$loop->last), @endif
                                        @endforeach
                                    </p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">{{ $item->quantity }} × ₺{{ number_format($item->price, 2) }}</p>
                                <p class="text-sm font-medium text-gray-900">₺{{ number_format($item->subtotal, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t pt-4 mt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">₺{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-medium">₺{{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tax</span>
                        <span class="font-medium">₺{{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-base font-bold">
                        <span>Total</span>
                        <span>₺{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Shipping Address</h2>
                <div class="text-sm text-gray-600">
                    <p class="font-medium text-gray-900">{{ $order->shipping_full_name }}</p>
                    <p>{{ $order->shipping_address_line1 }}</p>
                    @if($order->shipping_address_line2)
                        <p>{{ $order->shipping_address_line2 }}</p>
                    @endif
                    <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                    <p>{{ $order->shipping_country }}</p>
                    <p class="mt-2">Phone: {{ $order->shipping_phone }}</p>
                </div>
            </div>

            @if($order->shipment)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Shipment</h2>
                <div class="text-sm text-gray-600">
                    <p><span class="font-medium">Status:</span> {{ ucfirst($order->shipment->status) }}</p>
                    @if($order->shipment->courier_name)
                        <p><span class="font-medium">Courier:</span> {{ $order->shipment->courier_name }}</p>
                    @endif
                    @if($order->shipment->tracking_number)
                        <p><span class="font-medium">Tracking Number:</span> {{ $order->shipment->tracking_number }}</p>
                    @endif
                    @if($order->shipment->shipped_at)
                        <p><span class="font-medium">Shipped:</span> {{ $order->shipment->shipped_at->format('M d, Y H:i') }}</p>
                    @endif
                    @if($order->shipment->delivered_at)
                        <p><span class="font-medium">Delivered:</span> {{ $order->shipment->delivered_at->format('M d, Y H:i') }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment</h2>
                <div class="text-sm text-gray-600">
                    <p><span class="font-medium">Method:</span> {{ ucfirst($order->payment_method ?? 'N/A') }}</p>
                    <p><span class="font-medium">Status:</span> 
                        @if($order->payment)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->payment->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($order->payment->status) }}
                            </span>
                        @else
                            N/A
                        @endif
                    </p>
                    @if($order->payment && $order->payment->transaction_id)
                        <p><span class="font-medium">Transaction ID:</span> {{ $order->payment->transaction_id }}</p>
                    @endif
                </div>
            </div>

            @if($order->canBeCancelled() && !$order->cancelled_at)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>
                <form method="POST" action="{{ route('orders.cancel', $order->id) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cancellation Reason</label>
                        <textarea name="reason" rows="3" required class="w-full border rounded-lg px-3 py-2" placeholder="Please provide a reason for cancellation..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">
                        Cancel Order
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
