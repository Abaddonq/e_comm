@extends('layouts.web')

@section('title', ' - Cart')


@section('content')
<div class="cart-page">
    <div class="cart-container">
        <h1 class="cart-heading">{{ __('My Cart') }}</h1>

        @if(count($cartData['items']) > 0)
            <div class="cart-layout">
                <div class="cart-items-card">
                    <div class="cart-items-table-wrap">
                        <table class="cart-items-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Total') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartData['items'] as $item)
                                    <tr>
                                        <td>
                                            <div class="cart-product">
                                                @if($item['item']->variant->product->images->first())
                                                    <img
                                                        src="{{ asset('storage/' . $item['item']->variant->product->images->first()->path) }}"
                                                        alt="{{ $item['item']->variant->product->title }}"
                                                        class="cart-product-image"
                                                    >
                                                @else
                                                    <div class="cart-product-image-placeholder">{{ __('No Image') }}</div>
                                                @endif
                                                <div>
                                                    <div class="cart-product-title">{{ $item['item']->variant->product->title }}</div>
                                                    <div class="cart-product-sku">{{ $item['item']->variant->sku }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($item['price_changed'])
                                                <span class="price-old">₺{{ number_format($item['original_price'], 2) }}</span>
                                            @endif
                                            <span>₺{{ number_format($item['current_price'], 2) }}</span>
                                        </td>
                                        <td>
                                            <input
                                                type="number"
                                                value="{{ $item['item']->quantity }}"
                                                min="1"
                                                onchange="updateQuantity({{ $item['item']->id }}, this.value)"
                                                class="quantity-input"
                                            >
                                        </td>
                                        <td class="item-total">₺{{ number_format($item['subtotal'], 2) }}</td>
                                        <td>
                                            <button type="button" onclick="removeItem({{ $item['item']->id }})" class="remove-btn">{{ __('Remove') }}</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="cart-mobile-list">
                        @foreach($cartData['items'] as $item)
                            <div class="cart-mobile-item">
                                <div class="cart-mobile-top">
                                    @if($item['item']->variant->product->images->first())
                                        <img
                                            src="{{ asset('storage/' . $item['item']->variant->product->images->first()->path) }}"
                                            alt="{{ $item['item']->variant->product->title }}"
                                            class="cart-mobile-image"
                                        >
                                    @else
                                        <div class="cart-mobile-image-placeholder">{{ __('No Image') }}</div>
                                    @endif
                                    <div class="cart-mobile-meta">
                                        <div class="cart-mobile-title">{{ $item['item']->variant->product->title }}</div>
                                        <div class="cart-mobile-sku">{{ $item['item']->variant->sku }}</div>
                                    </div>
                                </div>

                                <div class="cart-mobile-grid">
                                    <div class="cart-mobile-row">
                                        <span class="cart-mobile-label">{{ __('Price') }}</span>
                                        <div class="cart-mobile-value">
                                            @if($item['price_changed'])
                                                <span class="price-old">₺{{ number_format($item['original_price'], 2) }}</span>
                                            @endif
                                            <span>₺{{ number_format($item['current_price'], 2) }}</span>
                                        </div>
                                    </div>

                                    <div class="cart-mobile-row">
                                        <span class="cart-mobile-label">{{ __('Total') }}</span>
                                        <div class="cart-mobile-value item-total">₺{{ number_format($item['subtotal'], 2) }}</div>
                                    </div>

                                    <div class="cart-mobile-row cart-mobile-row-full">
                                        <span class="cart-mobile-label">{{ __('Quantity') }}</span>
                                        <input
                                            type="number"
                                            value="{{ $item['item']->quantity }}"
                                            min="1"
                                            onchange="updateQuantity({{ $item['item']->id }}, this.value)"
                                            class="quantity-input cart-mobile-qty"
                                        >
                                    </div>
                                </div>

                                <div class="cart-mobile-actions">
                                    <button type="button" onclick="removeItem({{ $item['item']->id }})" class="remove-btn">{{ __('Remove') }}</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="cart-summary-card">
                    <h2 class="summary-title">{{ __('Order Summary') }}</h2>

                    <div class="summary-row">
                        <span>{{ __('Subtotal') }}</span>
                        <strong>₺{{ number_format($cartData['subtotal'], 2) }}</strong>
                    </div>
                    <div class="summary-row">
                        <span>{{ __('Item Count') }}</span>
                        <strong>{{ $cartData['item_count'] }}</strong>
                    </div>

                    <hr class="summary-divider">

                    <div class="summary-row summary-total">
                        <span>{{ __('Total') }}</span>
                        <strong>₺{{ number_format($cartData['subtotal'], 2) }}</strong>
                    </div>

                    @include('web.cart.partials.checkout-cta')
                </div>

                <div class="cart-mobile-bar">
                    <div class="cart-mobile-bar-total">
                        <span class="cart-mobile-bar-label">{{ __('Total') }}</span>
                        <strong class="cart-mobile-bar-value">₺{{ number_format($cartData['subtotal'], 2) }}</strong>
                    </div>
                    @include('web.cart.partials.checkout-cta')
                </div>
            </div>
        @else
            <div class="cart-empty">
                <p>{{ __('Your cart is empty.') }}</p>
                <a href="{{ route('home') }}" class="continue-link">{{ __('Continue Shopping') }}</a>
            </div>
        @endif
    </div>
</div>
@endsection
