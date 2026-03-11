@extends('layouts.web')

@section('title', ' - Order Success')

@section('content')
<div class="success-page">
    <div class="success-container">
        <div class="success-card">
            <div class="success-icon" aria-hidden="true">
                <svg width="34" height="34" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="success-title">{{ __('Order Received') }}</h1>
            <p class="success-subtitle">
                {{ __('Order success message') }} <strong>{{ $order->order_number }}</strong>
            </p>

            <div class="details-card">
                <h2 class="details-title">{{ __('Order Details') }}</h2>

                <div class="details-grid">
                    <div>
                        <p class="details-item-label">{{ __('Order No') }}</p>
                        <p class="details-item-value">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="details-item-label">{{ __('Status') }}</p>
                        <p class="details-item-value details-item-value-capitalize">{{ $order->status }}</p>
                    </div>
                    <div>
                        <p class="details-item-label">{{ __('Date') }}</p>
                        <p class="details-item-value">{{ $order->created_at->format('d.m.Y') }}</p>
                    </div>
                    <div>
                        <p class="details-item-label">{{ __('Total') }}</p>
                        <p class="details-item-value">₺{{ number_format($order->total, 2) }}</p>
                    </div>
                </div>

                <div class="address-box">
                    <p class="details-item-label">{{ __('Shipping Address Label') }}</p>
                    <div class="address-lines">
                        <p>{{ $order->shipping_full_name }}</p>
                        <p>{{ $order->shipping_address_line1 }}</p>
                        <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                        <p>{{ $order->shipping_country }}</p>
                    </div>
                </div>
            </div>

            <div class="success-actions">
                <a href="{{ route('home') }}" class="theme-btn theme-btn-primary">{{ __('Continue Shopping') }}</a>
                @auth
                    <a href="{{ route('profile.index', ['tab' => 'orders']) }}" class="theme-btn theme-btn-ghost">{{ __('My Orders') }}</a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
