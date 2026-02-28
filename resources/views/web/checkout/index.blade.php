@extends('layouts.web')

@section('content')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const cardFields = document.getElementById('card-fields');
    
    function toggleCardFields() {
        const selected = document.querySelector('input[name="payment_method"]:checked');
        if (selected && selected.value === 'iyzico') {
            cardFields.style.display = 'block';
        } else {
            cardFields.style.display = 'none';
        }
    }
    
    paymentMethods.forEach(method => {
        method.addEventListener('change', toggleCardFields);
    });
    
    // Initial state on page load
    toggleCardFields();
});
</script>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Checkout</h1>

    @if($cart->items->isEmpty())
        <div class="text-center py-12">
            <p class="text-gray-500 mb-4">Your cart is empty.</p>
            <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-800">
                Continue shopping
            </a>
        </div>
    @else
        <form action="{{ route('checkout.process') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Shipping Address</h2>
                        
                        @if(!Auth::check())
                            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded mb-4">
                                Please <a href="{{ route('login') }}" class="underline">login</a> to checkout or <a href="{{ route('register') }}" class="underline">register</a> to create an account.
                            </div>
                        @endif

                        @if($addresses->isEmpty() && Auth::check())
                            <div class="mb-4">
                                <a href="{{ route('addresses.create') }}" class="text-indigo-600 hover:text-indigo-800">
                                    + Add a new address
                                </a>
                            </div>
                        @endif

                        @error('address_id')
                            <p class="text-sm text-red-600 mb-2">{{ $message }}</p>
                        @enderror

                        <div class="space-y-4">
                            @foreach($addresses as $address)
                                <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('address_id') == $address->id ? 'border-indigo-500 bg-indigo-50' : '' }}">
                                    <input type="radio" name="address_id" value="{{ $address->id }}" {{ old('address_id') == $address->id ? 'checked' : '' }} class="mt-1">
                                    <div class="ml-3">
                                        <p class="font-medium">{{ $address->full_name }}</p>
                                        <p class="text-sm text-gray-600">{{ $address->address_line1 }}</p>
                                        @if($address->address_line2)
                                            <p class="text-sm text-gray-600">{{ $address->address_line2 }}</p>
                                        @endif
                                        <p class="text-sm text-gray-600">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                        <p class="text-sm text-gray-600">{{ $address->country }}</p>
                                        <p class="text-sm text-gray-600">{{ $address->phone }}</p>
                                        @if($address->is_default)
                                            <span class="inline-block mt-1 text-xs bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded">Default</span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        @if(Auth::check())
                            <div class="mt-4">
                                <a href="{{ route('addresses.create') }}" class="text-indigo-600 hover:text-indigo-800">
                                    + Add a new address
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Method</h2>
                        
                        @error('payment_method')
                            <p class="text-sm text-red-600 mb-2">{{ $message }}</p>
                        @enderror

                        <div class="space-y-3">
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('payment_method') == 'iyzico' ? 'border-indigo-500 bg-indigo-50' : '' }}">
                                <input type="radio" name="payment_method" value="iyzico" {{ old('payment_method') == 'iyzico' ? 'checked' : '' }} class="h-4 w-4 text-indigo-600">
                                <div class="ml-3">
                                    <p class="font-medium">Iyzico</p>
                                    <p class="text-sm text-gray-500">Pay securely with credit card</p>
                                </div>
                            </label>
                            
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('payment_method') == 'stripe' ? 'border-indigo-500 bg-indigo-50' : '' }}">
                                <input type="radio" name="payment_method" value="stripe" {{ old('payment_method') == 'stripe' ? 'checked' : '' }} class="h-4 w-4 text-indigo-600">
                                <div class="ml-3">
                                    <p class="font-medium">Stripe</p>
                                    <p class="text-sm text-gray-500">Pay with credit card via Stripe</p>
                                </div>
                            </label>
                        </div>

                        <div id="card-fields" class="mt-6 space-y-4" style="display: none;">
                            <h3 class="text-md font-medium text-gray-900">Card Information</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                                <input type="text" name="card_number" placeholder="5526 0800 0000 0005" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Card Holder Name</label>
                                <input type="text" name="card_holder" placeholder="John Doe"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                                    <select name="expire_month" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        @for($m = 1; $m <= 12; $m++)
                                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                                    <select name="expire_year" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        @for($y = date('Y'); $y <= date('Y') + 10; $y++)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                                    <input type="text" name="cvv" placeholder="123" maxlength="4"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <p class="text-xs text-gray-500">Test cards: 5526 0800 0000 0005 (success), 5526 0800 0000 0006 (fail)</p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                        
                        <div class="space-y-3 mb-4">
                            @foreach($cartData['items'] as $item)
                                <div class="flex justify-between text-sm">
                                    <div>
                                        <p class="text-gray-900">{{ $item['item']->variant->product->title }}</p>
                                        <p class="text-gray-500">{{ $item['item']->variant->sku ?? 'Variant' }} × {{ $item['item']->quantity }}</p>
                                        @if($item['price_changed'])
                                            <p class="text-xs text-yellow-600">Price changed</p>
                                        @endif
                                    </div>
                                    <p class="font-medium">₺{{ number_format($item['subtotal'], 2) }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium">₺{{ number_format($cartData['subtotal'], 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-medium">Calculated at next step</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tax</span>
                                <span class="font-medium">Calculated at next step</span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="flex items-start">
                                <input type="checkbox" name="terms_accepted" value="1" {{ old('terms_accepted') ? 'checked' : '' }} class="mt-1 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-600">
                                    I agree to the <a href="#" class="text-indigo-600 hover:underline">Terms and Conditions</a> and <a href="#" class="text-indigo-600 hover:underline">Privacy Policy</a>
                                </span>
                            </label>
                            @error('terms_accepted')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" {{ !Auth::check() ? 'disabled' : '' }}
                            class="w-full mt-6 bg-indigo-600 text-white py-3 rounded-md hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
                            {{ Auth::check() ? 'Proceed to Payment' : 'Login to Checkout' }}
                        </button>

                        @if(!Auth::check())
                            <p class="text-sm text-gray-500 text-center mt-2">You need to login to complete your purchase</p>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection
