@extends('layouts.web')

@section('title', ' - Checkout')

<style>
    .checkout-page {
        min-height: 100vh;
        padding-top: 85px;
        background: #fafafa;
    }

    .checkout-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 24px 80px;
    }

    .checkout-heading {
        font-size: 36px;
        font-weight: 400;
        letter-spacing: 0.04em;
        color: var(--color-secondary);
        margin-bottom: 28px;
    }

    .checkout-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 28px;
    }

    .checkout-main {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .panel {
        background: #fff;
        border: 1px solid var(--color-border);
        border-radius: 14px;
        padding: 22px;
    }

    .panel-title {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--color-muted);
        margin-bottom: 14px;
    }

    .notice {
        margin-bottom: 14px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 13px;
    }

    .notice a {
        color: inherit;
        font-weight: 600;
    }

    .error-text {
        font-size: 13px;
        color: #b91c1c;
        margin-bottom: 10px;
    }

    .text-link {
        color: var(--color-secondary);
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .text-link:hover {
        color: var(--color-hover);
    }

    .option-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .option-card {
        border: 1px solid var(--color-border);
        border-radius: 10px;
        padding: 14px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        cursor: pointer;
        transition: border-color var(--transition-fast), background var(--transition-fast);
    }

    .option-card.active {
        border-color: var(--color-secondary);
        background: #f7f7f7;
    }

    .option-card input[type="radio"] {
        margin-top: 3px;
        accent-color: #1a1a1a;
    }

    .option-title {
        font-size: 14px;
        font-weight: 500;
        color: var(--color-secondary);
        margin-bottom: 2px;
    }

    .option-sub {
        font-size: 12px;
        color: var(--color-muted);
        line-height: 1.6;
    }

    .option-badge {
        display: inline-block;
        margin-top: 8px;
        border: 1px solid var(--color-border);
        border-radius: 999px;
        padding: 2px 8px;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--color-secondary);
    }

    .card-fields {
        margin-top: 18px;
        padding-top: 18px;
        border-top: 1px solid var(--color-border);
    }

    .field-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .field {
        margin-bottom: 12px;
    }

    .field label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--color-muted);
        margin-bottom: 6px;
    }

    .field input,
    .field select {
        width: 100%;
        height: 44px;
        border-radius: 8px;
        border: 1px solid var(--color-border);
        background: #fff;
        padding: 0 12px;
        font-size: 14px;
        color: var(--color-secondary);
    }

    .field input:focus,
    .field select:focus {
        outline: none;
        border-color: var(--color-secondary);
    }

    .field-note {
        font-size: 12px;
        color: var(--color-muted);
        margin-top: 8px;
    }

    .checkout-summary {
        position: sticky;
        top: 110px;
        height: fit-content;
    }

    .summary-items {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 14px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        font-size: 13px;
    }

    .summary-item-title {
        color: var(--color-secondary);
        margin-bottom: 2px;
    }

    .summary-item-sub {
        color: var(--color-muted);
        font-size: 12px;
    }

    .price-warning {
        color: #92400e;
        font-size: 11px;
        margin-top: 4px;
    }

    .summary-rows {
        border-top: 1px solid var(--color-border);
        padding-top: 14px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        color: var(--color-secondary);
    }

    .summary-row span:last-child {
        font-weight: 600;
    }

    .terms-wrap {
        margin-top: 16px;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .terms-wrap input {
        margin-top: 2px;
        accent-color: #1a1a1a;
    }

    .terms-text {
        font-size: 12px;
        color: var(--color-muted);
    }

    .terms-text a {
        color: var(--color-secondary);
    }

    .theme-btn {
        width: 100%;
        margin-top: 16px;
        min-height: 48px;
        border-radius: 8px;
        border: 1px solid var(--color-secondary);
        background: var(--color-secondary);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        cursor: pointer;
        transition: background var(--transition-fast), border-color var(--transition-fast);
    }

    .theme-btn:hover {
        background: var(--color-hover);
        border-color: var(--color-hover);
    }

    .theme-btn:disabled {
        background: #9ca3af;
        border-color: #9ca3af;
        cursor: not-allowed;
    }

    .login-note {
        margin-top: 8px;
        font-size: 12px;
        color: var(--color-muted);
        text-align: center;
    }

    .empty-box {
        background: #fff;
        border: 1px solid var(--color-border);
        border-radius: 14px;
        text-align: center;
        padding: 74px 20px;
    }

    .empty-box p {
        font-size: 16px;
        color: var(--color-muted);
        margin-bottom: 16px;
    }

    @media (max-width: 1024px) {
        .checkout-grid {
            grid-template-columns: 1fr;
        }

        .checkout-summary {
            position: static;
        }
    }

    @media (max-width: 640px) {
        .checkout-page {
            padding-top: 70px;
        }

        .checkout-container {
            padding: 28px 16px 52px;
        }

        .checkout-heading {
            font-size: 28px;
            margin-bottom: 22px;
        }

        .field-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

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
                                    <div style="margin-top: 12px;">
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

                            <div id="card-fields" class="card-fields" style="display: none;">
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
                            <p class="error-text" style="margin-top: 8px;">{{ $message }}</p>
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

@section('scripts')
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

    function syncSelectableCards(groupSelector) {
        const group = document.querySelector('[data-selectable-group="' + groupSelector + '"]');
        if (!group) {
            return;
        }

        group.querySelectorAll('.option-card').forEach(card => {
            const input = card.querySelector('input[type="radio"]');
            card.classList.toggle('active', !!input && input.checked);
        });
    }

    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            toggleCardFields();
            syncSelectableCards('payment-method');
        });
    });

    const addressInputs = document.querySelectorAll('input[name="address_id"]');
    addressInputs.forEach(input => {
        input.addEventListener('change', function() {
            syncSelectableCards('address');
        });
    });

    toggleCardFields();
    syncSelectableCards('payment-method');
    syncSelectableCards('address');
});
</script>
@endsection
