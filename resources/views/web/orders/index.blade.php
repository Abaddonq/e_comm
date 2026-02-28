@extends('layouts.web')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">My Orders</h1>

    @if($orders->isEmpty())
        <div class="text-center py-12">
            <p class="text-gray-500 mb-4">You haven't placed any orders yet.</p>
            <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-800">
                Start shopping
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Order #{{ $order->order_number }}
                            </h3>
                            <p class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="text-right">
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
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">
                                {{ $order->items->count() }} item(s)
                            </p>
                            <p class="text-sm text-gray-500">
                                Ship to: {{ $order->shipping_full_name }}, {{ $order->shipping_city }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-gray-900">₺{{ number_format($order->total, 2) }}</p>
                            @if($order->shipment && $order->shipment->tracking_number)
                                <p class="text-sm text-indigo-600">
                                    Tracking: {{ $order->shipment->tracking_number }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('orders.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            View Details &rarr;
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
