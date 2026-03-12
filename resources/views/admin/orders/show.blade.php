@extends('layouts.admin')

@section('content')
<div class="px-6 py-8">
    <div class="flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-center mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Order {{ $order->order_number }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 min-h-[44px]">
            &larr; Back to Orders
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h2>
                <div class="admin-table-hint pb-2 text-xs text-gray-500">Swipe horizontally to view full table.</div>
                <div class="admin-table-scroll">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->product_title }}</div>
                                    @if($item->variant_attributes)
                                        <div class="text-xs text-gray-500">
                                            @foreach(json_decode($item->variant_attributes, true) ?? [] as $key => $value)
                                                {{ ucfirst($key) }}: {{ $value }}@if(!$loop->last), @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $item->variant_sku }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500 text-right">₺{{ number_format($item->price, 2) }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 text-right">₺{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-sm text-gray-500 text-right">Subtotal</td>
                            <td class="px-4 py-2 text-sm font-medium text-gray-900 text-right">₺{{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-sm text-gray-500 text-right">Shipping</td>
                            <td class="px-4 py-2 text-sm font-medium text-gray-900 text-right">₺{{ number_format($order->shipping_cost, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-sm text-gray-500 text-right">Tax</td>
                            <td class="px-4 py-2 text-sm font-medium text-gray-900 text-right">₺{{ number_format($order->tax, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-base font-bold text-gray-900 text-right">Total</td>
                            <td class="px-4 py-2 text-base font-bold text-gray-900 text-right">₺{{ number_format($order->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
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

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h2>
                @if($order->payment)
                    <div class="text-sm text-gray-600">
                        <p><span class="font-medium">Method:</span> {{ ucfirst($order->payment->payment_method) }}</p>
                        <p><span class="font-medium">Status:</span> 
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->payment->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($order->payment->status) }}
                            </span>
                        </p>
                        @if($order->payment->transaction_id)
                            <p><span class="font-medium">Transaction ID:</span> {{ $order->payment->transaction_id }}</p>
                        @endif
                        @if($order->payment->failure_reason)
                            <p><span class="font-medium text-red-600">Failure Reason:</span> {{ $order->payment->failure_reason }}</p>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-500">No payment information available.</p>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Status</h2>
                
                <div class="mb-4">
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $order->status_badge_class }}">
                        {{ $order->internal_status_label }}
                    </span>
                    <p class="mt-2 text-xs text-gray-500">Customer-facing label: {{ $order->customer_status_label }}</p>
                </div>

                <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Change Status</label>
                        <select name="status" class="w-full border rounded-lg px-3 py-2 min-h-[44px]">
                            @foreach(\App\Support\OrderStatusMapper::adminStatusOptions() as $code => $label)
                                @if($code !== \App\Support\OrderStatusMapper::FULFILLMENT_CANCELLED || $order->canBeCancelled())
                                    <option value="{{ $code }}" {{ $order->effective_fulfillment_status === $code ? 'selected' : '' }}>{{ $label }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    
                    <div id="cancellation-reason" class="mb-4 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cancellation Reason</label>
                        <textarea name="cancellation_reason" rows="2" class="w-full border rounded-lg px-3 py-2 min-h-[44px]" placeholder="Reason for cancellation..."></textarea>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 min-h-[44px]">
                        Update Status
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Shipment</h2>
                
                @if($order->shipment)
                    <div class="text-sm text-gray-600 mb-4">
                        <p><span class="font-medium">Courier:</span> {{ $order->shipment->courier_name }}</p>
                        <p><span class="font-medium">Status:</span> {{ ucfirst($order->shipment->status) }}</p>
                        @if($order->shipment->tracking_number)
                            <p><span class="font-medium">Tracking #:</span> {{ $order->shipment->tracking_number }}</p>
                        @endif
                        @if($order->shipment->shipped_at)
                            <p><span class="font-medium">Shipped:</span> {{ $order->shipment->shipped_at->format('M d, Y H:i') }}</p>
                        @endif
                        @if($order->shipment->delivered_at)
                            <p><span class="font-medium">Delivered:</span> {{ $order->shipment->delivered_at->format('M d, Y H:i') }}</p>
                        @endif
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.orders.update-shipment', $order->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tracking Number</label>
                        <input type="text" name="tracking_number" value="{{ $order->shipment->tracking_number ?? '' }}" 
                            class="w-full border rounded-lg px-3 py-2 min-h-[44px]">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Courier Name</label>
                        <input type="text" name="courier_name" value="{{ $order->shipment->courier_name ?? '' }}" 
                            class="w-full border rounded-lg px-3 py-2 min-h-[44px]" placeholder="e.g., UPS, FedEx, PTT">
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 min-h-[44px]">
                        {{ $order->shipment ? 'Update' : 'Create' }} Shipment
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Customer</h2>
                @if($order->user)
                    <div class="text-sm text-gray-600">
                        <p class="font-medium text-gray-900">{{ $order->user->name }}</p>
                        <p>{{ $order->user->email }}</p>
                        <p>Member since: {{ $order->user->created_at->format('M d, Y') }}</p>
                    </div>
                @else
                    <p class="text-sm text-gray-500">Guest order</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.querySelector('select[name="status"]').addEventListener('change', function() {
    const reasonDiv = document.getElementById('cancellation-reason');
    if (this.value === '{{ \App\Support\OrderStatusMapper::FULFILLMENT_CANCELLED }}') {
        reasonDiv.classList.remove('hidden');
    } else {
        reasonDiv.classList.add('hidden');
    }
});
</script>
@endsection
