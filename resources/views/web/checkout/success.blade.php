@extends('layouts.web')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-2xl mx-auto text-center">
        <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Order Placed Successfully!</h1>
        <p class="text-gray-600 mb-6">Thank you for your order. Your order number is <strong>{{ $order->order_number }}</strong></p>
        
        <div class="bg-white rounded-lg shadow p-6 text-left mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Details</h2>
            
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Order Number</p>
                    <p class="font-medium">{{ $order->order_number }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Status</p>
                    <p class="font-medium capitalize">{{ $order->status }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Date</p>
                    <p class="font-medium">{{ $order->created_at->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Total</p>
                    <p class="font-medium">₺{{ number_format($order->total, 2) }}</p>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t">
                <p class="text-gray-500 text-sm">Shipping Address</p>
                <p class="text-gray-900">{{ $order->shipping_full_name }}</p>
                <p class="text-gray-600">{{ $order->shipping_address_line1 }}</p>
                <p class="text-gray-600">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                <p class="text-gray-600">{{ $order->shipping_country }}</p>
            </div>
        </div>
        
        <div class="flex justify-center space-x-4">
            <a href="{{ route('home') }}" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Continue Shopping
            </a>
            @auth
                <a href="{{ route('orders.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                    View Orders
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
