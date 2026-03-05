@extends('layouts.web')

@section('title', ' - Cart')

<style>
    .cart-page {
        min-height: 100vh;
        padding-top: 85px;
        background: #fafafa;
    }

    .cart-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 24px 80px;
    }

    .cart-heading {
        font-size: 36px;
        font-weight: 400;
        letter-spacing: 0.04em;
        color: var(--color-secondary);
        margin-bottom: 28px;
    }

    .cart-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 28px;
    }

    .cart-items-card,
    .cart-summary-card {
        background: var(--color-primary);
        border: 1px solid var(--color-border);
        border-radius: 14px;
    }

    .cart-items-table-wrap {
        overflow-x: auto;
    }

    .cart-mobile-list {
        display: none;
    }

    .cart-mobile-item {
        padding: 16px;
        border-bottom: 1px solid var(--color-border);
    }

    .cart-mobile-item:last-child {
        border-bottom: none;
    }

    .cart-mobile-top {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .cart-mobile-image {
        width: 72px;
        height: 72px;
        border-radius: 10px;
        object-fit: cover;
        background: #f5f5f5;
        flex-shrink: 0;
    }

    .cart-mobile-image-placeholder {
        width: 72px;
        height: 72px;
        border-radius: 10px;
        border: 1px solid var(--color-border);
        background: #f8f8f8;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--color-muted);
        font-size: 11px;
        flex-shrink: 0;
    }

    .cart-mobile-meta {
        min-width: 0;
        flex: 1;
    }

    .cart-mobile-title {
        font-size: 14px;
        font-weight: 500;
        color: var(--color-secondary);
        margin-bottom: 3px;
        line-height: 1.4;
    }

    .cart-mobile-sku {
        font-size: 12px;
        color: var(--color-muted);
    }

    .cart-mobile-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 12px;
        margin-bottom: 12px;
    }

    .cart-mobile-row {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .cart-mobile-label {
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--color-muted);
    }

    .cart-mobile-value {
        font-size: 14px;
        color: var(--color-secondary);
    }

    .cart-mobile-qty {
        width: 100%;
        max-width: 110px;
    }

    .cart-mobile-actions {
        display: flex;
        justify-content: flex-end;
    }

    .cart-items-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 760px;
    }

    .cart-items-table thead th {
        padding: 18px 20px;
        border-bottom: 1px solid var(--color-border);
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        color: var(--color-muted);
        text-align: left;
    }

    .cart-items-table tbody td {
        padding: 18px 20px;
        border-bottom: 1px solid var(--color-border);
        vertical-align: middle;
    }

    .cart-items-table tbody tr:last-child td {
        border-bottom: none;
    }

    .cart-product {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .cart-product-image {
        width: 78px;
        height: 78px;
        border-radius: 10px;
        object-fit: cover;
        background: #f5f5f5;
        flex-shrink: 0;
    }

    .cart-product-image-placeholder {
        width: 78px;
        height: 78px;
        border-radius: 10px;
        border: 1px solid var(--color-border);
        background: #f8f8f8;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--color-muted);
        font-size: 11px;
        flex-shrink: 0;
    }

    .cart-product-title {
        font-size: 14px;
        font-weight: 500;
        color: var(--color-secondary);
        margin-bottom: 4px;
    }

    .cart-product-sku {
        font-size: 12px;
        color: var(--color-muted);
    }

    .price-old {
        color: #b91c1c;
        text-decoration: line-through;
        margin-right: 8px;
        font-size: 13px;
    }

    .quantity-input {
        width: 74px;
        height: 42px;
        border: 1px solid var(--color-border);
        border-radius: 8px;
        padding: 0 8px;
        text-align: center;
        font-size: 14px;
        color: var(--color-secondary);
        background: #fff;
    }

    .quantity-input:focus {
        outline: none;
        border-color: var(--color-secondary);
    }

    .item-total {
        font-size: 14px;
        font-weight: 600;
        color: var(--color-secondary);
    }

    .remove-btn {
        border: none;
        background: none;
        color: #b91c1c;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
    }

    .remove-btn:hover {
        color: #7f1d1d;
    }

    .cart-summary-card {
        padding: 24px;
        position: sticky;
        top: 110px;
        height: fit-content;
    }

    .cart-mobile-bar {
        display: none;
    }

    .summary-title {
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--color-muted);
        margin-bottom: 18px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
        color: var(--color-secondary);
        margin-bottom: 12px;
    }

    .summary-row strong {
        font-weight: 600;
    }

    .summary-divider {
        border: none;
        border-top: 1px solid var(--color-border);
        margin: 16px 0;
    }

    .summary-total {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .theme-btn {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 48px;
        padding: 12px 18px;
        border-radius: 8px;
        border: 1px solid var(--color-secondary);
        background: var(--color-secondary);
        color: #fff;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        transition: background var(--transition-fast), border-color var(--transition-fast);
    }

    .theme-btn:hover {
        background: var(--color-hover);
        border-color: var(--color-hover);
    }

    .cart-empty {
        background: #fff;
        border: 1px solid var(--color-border);
        border-radius: 14px;
        text-align: center;
        padding: 74px 20px;
    }

    .cart-empty p {
        font-size: 16px;
        color: var(--color-muted);
        margin-bottom: 16px;
    }

    .continue-link {
        display: inline-block;
        color: var(--color-secondary);
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .continue-link:hover {
        color: var(--color-hover);
    }

    @media (max-width: 1024px) {
        .cart-layout {
            grid-template-columns: 1fr;
        }

        .cart-summary-card {
            position: static;
        }
    }

    @media (max-width: 640px) {
        .cart-page {
            padding-top: 70px;
        }

        .cart-container {
            padding: 28px 16px 120px;
        }

        .cart-heading {
            font-size: 28px;
            margin-bottom: 22px;
        }

        .cart-items-table-wrap {
            display: none;
        }

        .cart-mobile-list {
            display: block;
        }

        .cart-summary-card {
            padding: 18px;
        }

        .cart-mobile-bar {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1200;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px calc(12px + env(safe-area-inset-bottom));
            background: #fff;
            border-top: 1px solid var(--color-border);
            box-shadow: 0 -8px 20px rgba(0, 0, 0, 0.08);
        }

        .cart-mobile-bar-total {
            min-width: 0;
        }

        .cart-mobile-bar-label {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--color-muted);
            margin-bottom: 2px;
        }

        .cart-mobile-bar-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--color-secondary);
            line-height: 1.2;
        }

        .cart-mobile-bar .theme-btn {
            width: auto;
            min-width: 180px;
            min-height: 44px;
            padding: 10px 14px;
            font-size: 11px;
            letter-spacing: 0.1em;
            white-space: nowrap;
        }
    }
</style>

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

                                    <div class="cart-mobile-row" style="grid-column: span 2;">
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

                    @auth
                        <a href="{{ route('checkout.index') }}" class="theme-btn">{{ __('Proceed to Checkout') }}</a>
                    @else
                        <a href="{{ route('login') . '?redirect=' . urlencode(route('checkout.index')) }}" class="theme-btn">{{ __('Login to Checkout') }}</a>
                    @endauth
                </div>

                <div class="cart-mobile-bar">
                    <div class="cart-mobile-bar-total">
                        <span class="cart-mobile-bar-label">{{ __('Total') }}</span>
                        <strong class="cart-mobile-bar-value">₺{{ number_format($cartData['subtotal'], 2) }}</strong>
                    </div>
                    @auth
                        <a href="{{ route('checkout.index') }}" class="theme-btn">{{ __('Proceed to Checkout') }}</a>
                    @else
                        <a href="{{ route('login') . '?redirect=' . urlencode(route('checkout.index')) }}" class="theme-btn">{{ __('Login to Checkout') }}</a>
                    @endauth
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
    if (confirm(window.__t['Confirm Remove Item'])) {
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
