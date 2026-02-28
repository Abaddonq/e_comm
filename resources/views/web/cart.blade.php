@extends('layouts.web')

@section('title', ' - Cart')

@section('content')
<div class="bg-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

        @if(count($cartData['items']) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($cartData['items'] as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($item['item']->variant->product->images->first())
                                        <img src="{{ asset('storage/' . $item['item']->variant->product->images->first()->path) }}" alt="" class="w-16 h-16 object-cover rounded mr-4">
                                        @endif
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $item['item']->variant->product->title }}</div>
                                            <div class="text-sm text-gray-500">{{ $item['item']->variant->sku }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($item['price_changed'])
                                    <span class="text-red-500 line-through">₺{{ number_format($item['original_price'], 2) }}</span>
                                    @endif
                                    <span class="ml-2">₺{{ number_format($item['current_price'], 2) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <input type="number" value="{{ $item['item']->quantity }}" min="1" 
                                        onchange="updateQuantity({{ $item['item']->id }}, this.value)"
                                        class="border rounded px-2 py-1 w-16 text-center">
                                </td>
                                <td class="px-6 py-4 font-medium">
                                    ₺{{ number_format($item['subtotal'], 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    <button onclick="removeItem({{ $item['item']->id }})" class="text-red-600 hover:text-red-900">Remove</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold mb-4">Order Summary</h2>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">₺{{ number_format($cartData['subtotal'], 2) }}</span>
                    </div>
                    <div class="flex justify-between mb-4">
                        <span class="text-gray-600">Items</span>
                        <span class="font-medium">{{ $cartData['item_count'] }}</span>
                    </div>
                    <hr class="my-4">
                    <div class="flex justify-between mb-4">
                        <span class="text-lg font-bold">Total</span>
                        <span class="text-lg font-bold">₺{{ number_format($cartData['subtotal'], 2) }}</span>
                    </div>
                    @auth
                    <a href="{{ route('checkout.index') }}" class="w-full bg-indigo-600 text-white py-3 rounded hover:bg-indigo-700 inline-block text-center">
                        Proceed to Checkout
                    </a>
                    @else
                    <a href="{{ route('login') . '?redirect=' . urlencode(route('checkout.index')) }}" class="w-full bg-indigo-600 text-white py-3 rounded hover:bg-indigo-700 inline-block text-center">
                        Login to Checkout
                    </a>
                    @endauth
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg mb-4">Your cart is empty</p>
            <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">Continue Shopping</a>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
function updateQuantity(itemId, quantity) {
    fetch('{{ route("cart.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            item_id: itemId,
            quantity: parseInt(quantity)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function removeItem(itemId) {
    if (confirm('Remove this item from cart?')) {
        fetch('{{ route("cart.remove") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                item_id: itemId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}
</script>
@endsection
