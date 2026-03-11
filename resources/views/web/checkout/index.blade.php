@extends('layouts.web')

@section('title', ' - Checkout')


@section('content')
<div class="checkout-page">
    <div class="checkout-container">
        <h1 class="checkout-heading">{{ __('Checkout') }}</h1>

        @if($cart->items->isEmpty())
            <div class="empty-box">
                <p>{{ __('Cart empty for checkout') }}</p>
                <a href="{{ route('home') }}" class="text-link">{{ __('Continue Shopping') }}</a>
            </div>
        @else
            <form action="{{ route('checkout.process') }}" method="POST">
                @csrf

                <div class="checkout-grid">
                    <div class="checkout-main">
                        <section class="panel">
                            <h2 class="panel-title">{{ __('Shipping Address') }}</h2>

                            @if(!Auth::check())
                                <div class="notice">
                                    {{ __('Login notice for checkout') }} <a href="{{ route('login') }}">{{ __('login') }}</a> {{ __('or') }} <a href="{{ route('register') }}">{{ __('create account') }}</a>.
                                </div>
                            @endif

                            @if($addresses->isEmpty() && Auth::check())
                                <a href="{{ route('addresses.create', ['redirect_to' => 'checkout']) }}" class="text-link">{{ __('+ Add New Address') }}</a>
                            @endif

                            @error('address_id')
                                <p class="error-text">{{ $message }}</p>
                            @enderror

                            @if($addresses->isNotEmpty())
                                <div class="option-list" data-selectable-group="address">
                                    @foreach($addresses as $address)
                                        <label class="option-card {{ old('address_id') == $address->id ? 'active' : '' }}">
                                            <input type="radio" name="address_id" value="{{ $address->id }}" {{ old('address_id') == $address->id ? 'checked' : '' }}>
                                            <span>
                                                <span class="option-title">{{ $address->full_name }}</span>
                                                <span class="option-sub">
                                                    {{ $address->address_line1 }}
                                                    @if($address->address_line2)
                                                        , {{ $address->address_line2 }}
                                                    @endif
                                                    <br>{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}
                                                    <br>{{ $address->country }} - {{ $address->phone }}
                                                </span>
                                                @if($address->is_default)
                                                    <span class="option-badge">{{ __('Default') }}</span>
                                                @endif
                                            </span>
                                        </label>
                                    @endforeach
                                </div>

                                @if(Auth::check())
                                    <div class="checkout-add-address-wrap">
                                        <a href="{{ route('addresses.create', ['redirect_to' => 'checkout']) }}" class="text-link">{{ __('+ Add New Address') }}</a>
                                    </div>
                                @endif
                            @endif
                        </section>

                        <section class="panel">
                            <h2 class="panel-title">{{ __('Payment Method') }}</h2>

                            @error('payment_method')
                                <p class="error-text">{{ $message }}</p>
                            @enderror

                            <div class="option-list" data-selectable-group="payment-method">
                                <label class="option-card {{ old('payment_method') == 'iyzico' ? 'active' : '' }}">
                                    <input type="radio" name="payment_method" value="iyzico" {{ old('payment_method') == 'iyzico' ? 'checked' : '' }}>
                                    <span>
                                        <span class="option-title">Iyzico</span>
                                        <span class="option-sub">{{ __('Secure payment with credit card') }}</span>
                                    </span>
                                </label>

                                <label class="option-card {{ old('payment_method') == 'stripe' ? 'active' : '' }}">
                                    <input type="radio" name="payment_method" value="stripe" {{ old('payment_method') == 'stripe' ? 'checked' : '' }}>
                                    <span>
                                        <span class="option-title">Stripe</span>
                                        <span class="option-sub">{{ __('Credit card payment via Stripe') }}</span>
                                    </span>
                                </label>
                            </div>

                            <div id="card-fields" class="card-fields card-fields-hidden">
                                <div class="field">
                                    <label for="card_number">{{ __('Card Number') }}</label>
                                    <input id="card_number" type="text" name="card_number" placeholder="5526 0800 0000 0005">
                                </div>

                                <div class="field">
                                    <label for="card_holder">{{ __('Name on Card') }}</label>
                                    <input id="card_holder" type="text" name="card_holder" placeholder="John Doe">
                                </div>

                                <div class="field-grid">
                                    <div class="field">
                                        <label for="expire_month">{{ __('Month') }}</label>
                                        <select id="expire_month" name="expire_month">
                                            @for($m = 1; $m <= 12; $m++)
                                                <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="field">
                                        <label for="expire_year">{{ __('Year') }}</label>
                                        <select id="expire_year" name="expire_year">
                                            @for($y = date('Y'); $y <= date('Y') + 10; $y++)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="field">
                                        <label for="cvv">CVV</label>
                                        <input id="cvv" type="text" name="cvv" placeholder="123" maxlength="4">
                                    </div>
                                </div>

                                <p class="field-note">{{ __('Test cards info') }}</p>
                            </div>
                        </section>
                    </div>

                    <aside class="panel checkout-summary">
                        <h2 class="panel-title">{{ __('Order Summary') }}</h2>

                        <div class="summary-items">
                            @foreach($cartData['items'] as $item)
                                <div class="summary-item">
                                    <div>
                                        <p class="summary-item-title">{{ $item['item']->variant->product->title }}</p>
                                        <p class="summary-item-sub">{{ $item['item']->variant->sku ?? __('Variant') }} x {{ $item['item']->quantity }}</p>
                                        @if($item['price_changed'])
                                            <p class="price-warning">{{ __('Price change detected') }}</p>
                                        @endif
                                    </div>
                                    <p class="summary-item-title">₺{{ number_format($item['subtotal'], 2) }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="summary-rows">
                            <div class="summary-row">
                                <span>{{ __('Subtotal') }}</span>
                                <span>₺{{ number_format($cartData['subtotal'], 2) }}</span>
                            </div>
                            <div class="summary-row">
                                <span>{{ __('Shipping') }}</span>
                                <span>{{ __('Calculated at next step') }}</span>
                            </div>
                            <div class="summary-row">
                                <span>{{ __('Tax') }}</span>
                                <span>{{ __('Calculated at next step') }}</span>
                            </div>
                        </div>

                        <div class="terms-wrap">
                            <input type="checkbox" name="terms_accepted" id="terms_accepted" value="1" {{ old('terms_accepted') ? 'checked' : '' }}>
                            <label for="terms_accepted" class="terms-text">
                                <span>{{ __('Terms acceptance text') }}</span>
                                <a href="#">{{ __('Details') }}</a>
                            </label>
                        </div>
                        @error('terms_accepted')
                            <p class="error-text terms-error-text">{{ $message }}</p>
                        @enderror

                        <button type="submit" class="theme-btn" {{ !Auth::check() ? 'disabled' : '' }}>
                            {{ Auth::check() ? __('Continue to Payment') : __('Login to Continue') }}
                        </button>

                        @if(!Auth::check())
                            <p class="login-note">{{ __('Login required for order') }}</p>
                        @endif
                    </aside>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
