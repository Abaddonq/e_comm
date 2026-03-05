@extends('layouts.web')

@section('title', ' - Order Success')

<style>
    .success-page {
        min-height: 100vh;
        padding-top: 85px;
        background: #fafafa;
    }

    .success-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 24px 80px;
    }

    .success-card {
        max-width: 780px;
        margin: 0 auto;
        background: #fff;
        border: 1px solid var(--color-border);
        border-radius: 14px;
        padding: 36px 28px;
    }

    .success-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 18px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #16a34a33;
        background: #16a34a1a;
        color: #15803d;
    }

    .success-title {
        font-size: 32px;
        font-weight: 400;
        letter-spacing: 0.03em;
        color: var(--color-secondary);
        text-align: center;
        margin-bottom: 10px;
    }

    .success-subtitle {
        text-align: center;
        font-size: 14px;
        color: var(--color-muted);
        margin-bottom: 26px;
    }

    .success-subtitle strong {
        color: var(--color-secondary);
        font-weight: 600;
    }

    .details-card {
        border: 1px solid var(--color-border);
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 22px;
        background: #fff;
    }

    .details-title {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--color-muted);
        margin-bottom: 14px;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .details-item-label {
        font-size: 11px;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--color-muted);
        margin-bottom: 4px;
    }

    .details-item-value {
        font-size: 14px;
        color: var(--color-secondary);
        font-weight: 500;
    }

    .address-box {
        border-top: 1px solid var(--color-border);
        margin-top: 16px;
        padding-top: 16px;
    }

    .address-lines {
        font-size: 14px;
        color: var(--color-secondary);
        line-height: 1.7;
    }

    .success-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .theme-btn {
        min-height: 46px;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        transition: background var(--transition-fast), border-color var(--transition-fast), color var(--transition-fast);
    }

    .theme-btn-primary {
        background: var(--color-secondary);
        border: 1px solid var(--color-secondary);
        color: #fff;
    }

    .theme-btn-primary:hover {
        background: var(--color-hover);
        border-color: var(--color-hover);
    }

    .theme-btn-ghost {
        background: #fff;
        border: 1px solid var(--color-border);
        color: var(--color-secondary);
    }

    .theme-btn-ghost:hover {
        border-color: var(--color-secondary);
    }

    @media (max-width: 640px) {
        .success-page {
            padding-top: 70px;
        }

        .success-container {
            padding: 28px 16px 52px;
        }

        .success-card {
            padding: 28px 18px;
        }

        .success-title {
            font-size: 26px;
        }

        .details-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@section('content')
<div class="success-page">
    <div class="success-container">
        <div class="success-card">
            <div class="success-icon" aria-hidden="true">
                <svg width="34" height="34" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="success-title">Siparisiniz Alindi</h1>
            <p class="success-subtitle">
                Siparisiniz basariyla olusturuldu. Siparis numaraniz: <strong>{{ $order->order_number }}</strong>
            </p>

            <div class="details-card">
                <h2 class="details-title">Siparis Detaylari</h2>

                <div class="details-grid">
                    <div>
                        <p class="details-item-label">Siparis No</p>
                        <p class="details-item-value">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="details-item-label">Durum</p>
                        <p class="details-item-value" style="text-transform: capitalize;">{{ $order->status }}</p>
                    </div>
                    <div>
                        <p class="details-item-label">Tarih</p>
                        <p class="details-item-value">{{ $order->created_at->format('d.m.Y') }}</p>
                    </div>
                    <div>
                        <p class="details-item-label">Toplam</p>
                        <p class="details-item-value">₺{{ number_format($order->total, 2) }}</p>
                    </div>
                </div>

                <div class="address-box">
                    <p class="details-item-label">Teslimat Adresi</p>
                    <div class="address-lines">
                        <p>{{ $order->shipping_full_name }}</p>
                        <p>{{ $order->shipping_address_line1 }}</p>
                        <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                        <p>{{ $order->shipping_country }}</p>
                    </div>
                </div>
            </div>

            <div class="success-actions">
                <a href="{{ route('home') }}" class="theme-btn theme-btn-primary">Alisverise Devam Et</a>
                @auth
                    <a href="{{ route('profile.index', ['tab' => 'orders']) }}" class="theme-btn theme-btn-ghost">Siparislerim</a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
