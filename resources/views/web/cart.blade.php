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
            padding: 28px 16px 52px;
        }

        .cart-heading {
            font-size: 28px;
            margin-bottom: 22px;
        }
    }
</style>

@section('content')
<div class="cart-page">
    <div class="cart-container">
        <h1 class="cart-heading">Sepetim</h1>

        @if(count($cartData['items']) > 0)
            <div class="cart-layout">
                <div class="cart-items-card">
                    <div class="cart-items-table-wrap">
                        <table class="cart-items-table">
                            <thead>
                                <tr>
                                    <th>Urun</th>
                                    <th>Fiyat</th>
                                    <th>Adet</th>
                                    <th>Toplam</th>
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
                                            <button type="button" onclick="removeItem({{ $item['item']->id }})" class="remove-btn">Kaldir</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="cart-summary-card">
                    <h2 class="summary-title">Siparis Ozeti</h2>

                    <div class="summary-row">
                        <span>Ara Toplam</span>
                        <strong>₺{{ number_format($cartData['subtotal'], 2) }}</strong>
                    </div>
                    <div class="summary-row">
                        <span>Urun Sayisi</span>
                        <strong>{{ $cartData['item_count'] }}</strong>
                    </div>

                    <hr class="summary-divider">

                    <div class="summary-row summary-total">
                        <span>Toplam</span>
                        <strong>₺{{ number_format($cartData['subtotal'], 2) }}</strong>
                    </div>

                    @auth
                        <a href="{{ route('checkout.index') }}" class="theme-btn">Odeme Adimina Gec</a>
                    @else
                        <a href="{{ route('login') . '?redirect=' . urlencode(route('checkout.index')) }}" class="theme-btn">Giris Yap ve Devam Et</a>
                    @endauth
                </div>
            </div>
        @else
            <div class="cart-empty">
                <p>Sepetinizde henuz urun bulunmuyor.</p>
                <a href="{{ route('home') }}" class="continue-link">Alisverise Devam Et</a>
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
    if (confirm('Sepetten bu urunu kaldirmak istiyor musunuz?')) {
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
