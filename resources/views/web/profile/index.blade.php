@extends('layouts.web')

@section('title', ' - ' . __('My Account'))

@php
$activeTab = request()->query('tab', 'account');
@endphp

@section('content')
<style>
    .profile-page {
        padding-top: 85px;
        min-height: 100vh;
        background: #fafafa;
    }

    .profile-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 24px 80px;
    }

    .profile-header {
        margin-bottom: 32px;
    }

    .profile-header h1 {
        font-size: 28px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 8px;
    }

    .profile-header p {
        color: #666;
        font-size: 14px;
    }

    .profile-layout {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 32px;
    }

    @media (max-width: 768px) {
        .profile-layout {
            grid-template-columns: 1fr;
        }
    }

    .profile-sidebar {
        background: white;
        border-radius: 12px;
        padding: 24px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        height: fit-content;
    }

    .profile-sidebar.mobile {
        display: none;
    }

    @media (max-width: 768px) {
        .profile-sidebar.desktop {
            display: none;
        }
        .profile-sidebar.mobile {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
        }
    }

    .profile-nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        padding: 14px 16px;
        border: none;
        background: none;
        font-size: 14px;
        color: #666;
        cursor: pointer;
        border-radius: 8px;
        text-align: left;
        transition: all 0.2s;
    }

    .profile-nav-item:hover {
        background: #f5f5f5;
        color: #1a1a1a;
    }

    .profile-nav-item.active {
        background: #1a1a1a;
        color: white;
    }

    .profile-nav-item svg {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }

    .profile-content {
        min-height: 500px;
    }

    .profile-section {
        display: none;
    }

    .profile-section.active {
        display: block;
    }

    .section-title {
        font-size: 20px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 24px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #333;
        margin-bottom: 8px;
    }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.2s;
    }

    .form-input:focus {
        outline: none;
        border-color: #1a1a1a;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    @media (max-width: 640px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }

    .btn-group {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary {
        background: #1a1a1a;
        color: white;
    }

    .btn-primary:hover {
        background: #333;
    }

    .btn-secondary {
        background: #f5f5f5;
        color: #333;
    }

    .btn-secondary:hover {
        background: #eee;
    }

    .btn-danger {
        background: #dc2626;
        color: white;
    }

    .btn-danger:hover {
        background: #b91c1c;
    }

    .btn-sm {
        padding: 8px 16px;
        font-size: 13px;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-success {
        background: #ecfdf5;
        color: #059669;
    }

    .alert-error {
        background: #fef2f2;
        color: #dc2626;
    }

    /* Orders */
    .orders-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .order-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .order-number {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
    }

    .order-date {
        font-size: 13px;
        color: #666;
        margin-top: 4px;
    }

    .order-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .order-status.pending { background: #fef3c7; color: #92400e; }
    .order-status.paid { background: #dbeafe; color: #1e40af; }
    .order-status.processing { background: #e0e7ff; color: #3730a3; }
    .order-status.shipped { background: #d1fae5; color: #065f46; }
    .order-status.delivered { background: #d1fae5; color: #065f46; }
    .order-status.cancelled { background: #fef2f2; color: #991b1b; }

    .order-products-preview {
        display: flex;
        gap: 8px;
        margin-bottom: 16px;
    }

    .order-product-thumb {
        width: 64px;
        height: 64px;
        border-radius: 8px;
        overflow: hidden;
        background: #f5f5f5;
    }

    .order-product-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .order-product-thumb.placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
    }

    .order-product-more {
        width: 64px;
        height: 64px;
        border-radius: 8px;
        background: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        color: #666;
    }

    .order-summary {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        border-top: 1px solid #eee;
    }

    .order-items-count {
        font-size: 14px;
        color: #666;
    }

    .order-total {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
    }

    .order-actions {
        margin-top: 16px;
    }

    /* Wishlist */
    .wishlist-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    @media (max-width: 1024px) {
        .wishlist-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 640px) {
        .wishlist-grid { grid-template-columns: 1fr; }
    }

    .wishlist-item {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .wishlist-image {
        display: block;
        aspect-ratio: 1;
        background: #f5f5f5;
    }

    .wishlist-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .wishlist-image .no-image {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
    }

    .wishlist-info {
        padding: 16px;
    }

    .wishlist-info h3 {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 8px;
    }

    .wishlist-info h3 a {
        color: #1a1a1a;
        text-decoration: none;
    }

    .wishlist-price {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .wishlist-actions {
        display: flex;
        gap: 8px;
    }

    .empty-state, .empty-wishlist {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
    }

    .empty-state svg, .empty-wishlist svg {
        width: 64px;
        height: 64px;
        color: #ccc;
        margin-bottom: 16px;
    }

    .empty-state h3, .empty-wishlist h3 {
        font-size: 18px;
        color: #333;
        margin-bottom: 8px;
    }

    .empty-state p, .empty-wishlist p {
        color: #666;
        margin-bottom: 24px;
    }

    /* Addresses */
    .addresses-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    @media (max-width: 768px) {
        .addresses-grid { grid-template-columns: 1fr; }
    }

    .address-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        position: relative;
    }

    .address-card.default {
        border: 2px solid #1a1a1a;
    }

    .address-default-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #1a1a1a;
        color: white;
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 4px;
    }

    .address-name {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 4px;
    }

    .address-phone {
        font-size: 14px;
        color: #666;
        margin-bottom: 8px;
    }

    .address-text {
        font-size: 14px;
        color: #333;
        line-height: 1.5;
    }

    .address-actions {
        display: flex;
        gap: 12px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #eee;
    }

    .address-actions button {
        background: none;
        border: none;
        font-size: 13px;
        color: #666;
        cursor: pointer;
        padding: 0;
    }

    .address-actions button:hover {
        color: #1a1a1a;
    }

    .address-actions .delete-btn:hover {
        color: #dc2626;
    }

    .add-address-card {
        background: white;
        border: 2px dashed #ddd;
        border-radius: 12px;
        padding: 40px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        min-height: 180px;
    }

    .add-address-card:hover {
        border-color: #1a1a1a;
    }

    .add-address-card svg {
        width: 32px;
        height: 32px;
        color: #999;
        margin-bottom: 8px;
    }

    .add-address-card span {
        font-size: 14px;
        color: #666;
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal {
        background: white;
        border-radius: 16px;
        width: 100%;
        max-width: 560px;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 600;
    }

    .modal-close {
        background: none;
        border: none;
        cursor: pointer;
        color: #666;
        padding: 4px;
    }

    .modal-body {
        padding: 24px;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        cursor: pointer;
    }

    .checkbox-label input {
        width: 18px;
        height: 18px;
    }

    .warning-text {
        font-size: 14px;
        color: #666;
        margin-bottom: 20px;
    }

    .logout-section, .delete-account-section {
        background: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }
</style>

<div class="profile-page">
    <div class="profile-container">
        <div class="profile-header">
            <h1>{{ __('My Account') }}</h1>
            <p>{{ __('Manage your profile, orders and addresses') }}</p>
        </div>

        <div class="profile-layout">
            <!-- Sidebar -->
            <div class="profile-sidebar desktop">
                <button class="profile-nav-item {{ $activeTab === 'account' ? 'active' : '' }}" onclick="switchTab('account')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ __('Account Information') }}
                </button>
                <button class="profile-nav-item {{ $activeTab === 'orders' ? 'active' : '' }}" onclick="switchTab('orders')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    {{ __('My Orders') }}
                </button>
                <button class="profile-nav-item {{ $activeTab === 'wishlist' ? 'active' : '' }}" onclick="switchTab('wishlist')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    {{ __('My Wishlist') }}
                </button>
                <button class="profile-nav-item {{ $activeTab === 'addresses' ? 'active' : '' }}" onclick="switchTab('addresses')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ __('My Addresses') }}
                </button>
                <button class="profile-nav-item {{ $activeTab === 'logout' ? 'active' : '' }}" onclick="switchTab('logout')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    {{ __('Logout') }}
                </button>
            </div>

            <!-- Mobile Tabs -->
            <div class="profile-sidebar mobile">
                <button class="profile-nav-item {{ $activeTab === 'account' ? 'active' : '' }}" onclick="switchTab('account')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ __('Account') }}
                </button>
                <button class="profile-nav-item {{ $activeTab === 'orders' ? 'active' : '' }}" onclick="switchTab('orders')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    {{ __('Orders') }}
                </button>
                <button class="profile-nav-item {{ $activeTab === 'wishlist' ? 'active' : '' }}" onclick="switchTab('wishlist')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    {{ __('Favorites') }}
                </button>
                <button class="profile-nav-item {{ $activeTab === 'addresses' ? 'active' : '' }}" onclick="switchTab('addresses')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ __('Addresses') }}
                </button>
                <button class="profile-nav-item {{ $activeTab === 'logout' ? 'active' : '' }}" onclick="switchTab('logout')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    {{ __('Logout') }}
                </button>
            </div>

            <!-- Content -->
            <div class="profile-content">
                <!-- Account Section -->
                <div class="profile-section {{ $activeTab === 'account' ? 'active' : '' }}" id="section-account">
                    <h2 class="section-title">{{ __('Account Information') }}</h2>
                    
                    <div class="alert alert-success" id="profileAlert"></div>
                    
                    <form id="profileForm">
                        <div class="form-group">
                            <label class="form-label">{{ __('Full Name') }}</label>
                            <input type="text" name="name" class="form-input" value="{{ $user->name }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input type="email" name="email" class="form-input" value="{{ $user->email }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="tel" name="phone" class="form-input" value="{{ $user->phone ?? '' }}" placeholder="{{ __('Your phone number') }}">
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">{{ __('Update Information') }}</button>
                        </div>
                    </form>

                    <h2 class="section-title" style="margin-top: 48px;">{{ __('Change Password') }}</h2>
                    
                    <div class="alert alert-success" id="passwordAlert"></div>
                    <div class="alert alert-error" id="passwordError"></div>
                    
                    <form id="passwordForm">
                        <div class="form-group">
                            <label class="form-label">{{ __('Current Password') }}</label>
                            <input type="password" name="current_password" class="form-input" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('New Password') }}</label>
                                <input type="password" name="password" class="form-input" required minlength="8">
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Password Confirm') }}</label>
                                <input type="password" name="password_confirmation" class="form-input" required>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">{{ __('Update Password') }}</button>
                        </div>
                    </form>
                </div>

                <!-- Orders Section -->
                <div class="profile-section {{ $activeTab === 'orders' ? 'active' : '' }}" id="section-orders">
                    <h2 class="section-title">{{ __('My Orders') }}</h2>
                    
                    @if($orders->count() > 0)
                        <div class="orders-list">
                            @foreach($orders as $order)
                                <div class="order-card">
                                    <div class="order-header">
                                        <div>
                                            <div class="order-number">{{ __('Order') }} #{{ $loop->iteration }}</div>
                                            <div class="order-date">{{ $order->created_at->format('d.m.Y') }}</div>
                                        </div>
                                        <span class="order-status {{ $order->status }}">
                                            @switch($order->status)
                                                @case('pending') {{ __('Pending') }} @break
                                                @case('paid') {{ __('Paid') }} @break
                                                @case('processing') {{ __('Processing') }} @break
                                                @case('shipped') {{ __('Shipped') }} @break
                                                @case('delivered') {{ __('Delivered') }} @break
                                                @case('cancelled') {{ __('Cancelled') }} @break
                                                @default {{ $order->status }}
                                            @endswitch
                                        </span>
                                    </div>
                                    <div class="order-products-preview">
                                        @foreach($order->items->take(4) as $item)
                                            @php
                                                $product = $item->variant->product ?? null;
                                                $image = $product && $product->images->first() ? $product->images->first() : null;
                                            @endphp
                                            @if($image)
                                                <div class="order-product-thumb">
                                                    <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $product->title }}">
                                                </div>
                                            @else
                                                <div class="order-product-thumb placeholder">
                                                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                            @endif
                                        @endforeach
                                        @if($order->items->count() > 4)
                                            <div class="order-product-more">+{{ $order->items->count() - 4 }}</div>
                                        @endif
                                    </div>
                                    <div class="order-summary">
                                        <span class="order-items-count">{{ $order->items->count() }} {{ __('products_count') }}</span>
                                        <span class="order-total">₺{{ number_format($order->total, 2) }}</span>
                                    </div>
                                    <div class="order-actions">
                                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-secondary btn-sm">{{ __('Order Details') }}</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <h3>{{ __('No Orders Yet') }}</h3>
                            <p>{{ __('Start shopping to place your first order') }}</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">{{ __('Start Shopping') }}</a>
                        </div>
                    @endif
                </div>

                <!-- Wishlist Section -->
                <div class="profile-section {{ $activeTab === 'wishlist' ? 'active' : '' }}" id="section-wishlist">
                    <h2 class="section-title">{{ __('My Wishlist') }}</h2>
                    
                    <div class="wishlist-grid" id="wishlistGrid">
                        @php
                        $wishlistProducts = \App\Models\Wishlist::where('user_id', auth()->id())->with('product.images', 'product.variants')->get();
                        @endphp
                        
                        @if($wishlistProducts->count() > 0)
                            @foreach($wishlistProducts as $wishlist)
                                @php
                                    $product = $wishlist->product;
                                    if (!$product) continue;
                                    $minPrice = $product->variants->count() > 0 ? $product->variants->min('price') : 0;
                                    $image = $product->images->first();
                                @endphp
                                <div class="wishlist-item">
                                    <a href="{{ route('product.show', $product->slug) }}" class="wishlist-image">
                                        @if($image)
                                            <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $product->title }}">
                                        @else
                                            <div class="no-image">{{ __('No Image') }}</div>
                                        @endif
                                    </a>
                                    <div class="wishlist-info">
                                        <h3><a href="{{ route('product.show', $product->slug) }}">{{ $product->title }}</a></h3>
                                        <div class="wishlist-price">₺{{ number_format($minPrice, 2) }}</div>
                                        <div class="wishlist-actions">
                                            <button class="btn btn-sm btn-primary" onclick="quickAdd({{ $product->id }}, event)">{{ __('Add to Cart') }}</button>
                                            <button class="btn btn-sm btn-secondary" onclick="removeFromWishlist({{ $product->id }})">{{ __('Remove') }}</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-wishlist">
                                <svg width="64" height="64" fill="none" stroke="#ccc" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                <h3>{{ __('No Wishlist Yet') }}</h3>
                                <p>{{ __('Add products you like to your wishlist') }}</p>
                                <a href="{{ route('home') }}" class="btn btn-primary">{{ __('Start Shopping') }}</a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Addresses Section -->
                <div class="profile-section {{ $activeTab === 'addresses' ? 'active' : '' }}" id="section-addresses">
                    <h2 class="section-title">{{ __('My Addresses') }}</h2>
                    
                    <div class="addresses-grid">
                        @foreach($addresses as $address)
                            <div class="address-card {{ $address->is_default ? 'default' : '' }}">
                                @if($address->is_default)
                                    <span class="address-default-badge">{{ __('Default') }}</span>
                                @endif
                                <div class="address-name">{{ $address->full_name }}</div>
                                <div class="address-phone">{{ $address->phone }}</div>
                                <div class="address-text">{{ $address->full_address }}</div>
                                <div class="address-actions">
                                    <button onclick="editAddress({{ $address->id }})">{{ __('Edit') }}</button>
                                    @if(!$address->is_default)
                                        <button onclick="setDefaultAddress({{ $address->id }})">{{ __('Set as default address') }}</button>
                                    @endif
                                    <button class="delete-btn" onclick="deleteAddress({{ $address->id }})">{{ __('Delete') }}</button>
                                </div>
                            </div>
                        @endforeach
                        <div class="add-address-card" onclick="openModal()">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            <span>{{ __('+ Add New Address') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Logout Section -->
                <div class="profile-section {{ $activeTab === 'logout' ? 'active' : '' }}" id="section-logout">
                    <div class="logout-section">
                        <h2 class="section-title">{{ __('Logout') }}</h2>
                        <p>{{ __('Are you sure you want to log out?') }}</p>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('Are you sure you want to log out?') }}')">
                                {{ __('Logout') }}
                            </button>
                        </form>
                    </div>
                    
                    <div class="delete-account-section">
                        <h2 class="section-title">{{ __('Delete Account') }}</h2>
                        <p class="warning-text">{{ __('Deleting your account will permanently remove all your data. This action cannot be undone.') }}</p>
                        
                        <div class="alert alert-error" id="deleteAccountError" style="display: none;"></div>
                        <div class="alert alert-success" id="deleteAccountSuccess" style="display: none;"></div>
                        
                        <form id="deleteAccountForm">
                            <div class="form-group">
                                <label class="form-label">{{ __('Confirm your password') }}</label>
                                <input type="password" name="password" class="form-input" placeholder="{{ __('Password') }}" required>
                            </div>
                            <div class="btn-group">
                                <button type="submit" class="btn btn-danger">{{ __('Delete My Account') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Address Modal -->
<div class="modal-overlay" id="addressModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">{{ __('New Address') }}</h3>
            <button class="modal-close" onclick="closeModal()">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <form id="addressForm">
                <input type="hidden" id="addressId">
                <div class="form-group">
                    <label class="form-label">{{ __('Full Name') }}</label>
                    <input type="text" id="fullName" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Phone') }}</label>
                    <input type="tel" id="phone" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Address Line 1') }}</label>
                    <input type="text" id="addressLine1" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Address Line 2 (Optional)') }}</label>
                    <input type="text" id="addressLine2" class="form-input">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">{{ __('City') }}</label>
                        <input type="text" id="city" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('District') }}</label>
                        <input type="text" id="state" class="form-input">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">{{ __('Postal Code') }}</label>
                        <input type="text" id="postalCode" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('Country') }}</label>
                        <input type="text" id="country" class="form-input" value="Turkey" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="isDefault">
                        {{ __('Set as default address') }}
                    </label>
                </div>
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">{{ __('Save Address') }}</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">{{ __('Cancel') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function switchTab(tab) {
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tab);
        window.location.href = url.toString();
    }

    function removeFromWishlist(productId) {
        fetch('{{ route("wishlist.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ 
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(window.__t['Product removed from wishlist'], 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast(window.__t['An error occurred'], 'error');
        });
    }

    // Profile form
    document.getElementById('profileForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        try {
            const response = await fetch('{{ route("profile.update-profile") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(formData)),
            });
            
            const data = await response.json();
            const alert = document.getElementById('profileAlert');
            
            if (data.success) {
                alert.textContent = data.message;
                alert.style.display = 'block';
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });

    // Password form
    document.getElementById('passwordForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const successAlert = document.getElementById('passwordAlert');
        const errorAlert = document.getElementById('passwordError');
        
        successAlert.style.display = 'none';
        errorAlert.style.display = 'none';
        
        try {
            const response = await fetch('{{ route("profile.update-password") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(formData)),
            });
            
            const data = await response.json();
            
            if (data.success) {
                successAlert.textContent = data.message;
                successAlert.style.display = 'block';
                e.target.reset();
            } else {
                errorAlert.textContent = data.errors?.current_password?.[0] || window.__t['An error occurred'];
                errorAlert.style.display = 'block';
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });

    // Address modal
    function openModal() {
        document.getElementById('addressModal').classList.add('active');
        document.getElementById('modalTitle').textContent = @json(__('New Address'));
        document.getElementById('addressForm').reset();
        document.getElementById('addressId').value = '';
    }

    function closeModal() {
        document.getElementById('addressModal').classList.remove('active');
    }

    document.getElementById('addressForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const addressId = document.getElementById('addressId').value;
        const isEdit = addressId !== '';
        
        const data = {
            full_name: document.getElementById('fullName').value,
            phone: document.getElementById('phone').value,
            address_line1: document.getElementById('addressLine1').value,
            address_line2: document.getElementById('addressLine2').value,
            city: document.getElementById('city').value,
            state: document.getElementById('state').value,
            postal_code: document.getElementById('postalCode').value,
            country: document.getElementById('country').value,
            is_default: document.getElementById('isDefault').checked,
        };

        const url = isEdit 
            ? `/addresses/${addressId}`
            : '{{ route("addresses.store") }}';
        
        try {
            const response = await fetch(url, {
                method: isEdit ? 'PUT' : 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            });
            
            const result = await response.json();
            
            if (response.ok && result.success) {
                closeModal();
                window.location.reload();
            } else {
                let errorMsg = result.message || window.__t['An error occurred'];
                if (result.errors) {
                    errorMsg = Object.values(result.errors).flat().join('\n');
                }
                alert(errorMsg);
            }
        } catch (error) {
            console.error('Error:', error);
            alert(window.__t['An error occurred'] + ': ' + error.message);
        }
    });

    function editAddress(id) {
        const addresses = @json($addresses);
        const address = addresses.find(a => a.id === id);
        
        if (address) {
            document.getElementById('addressId').value = address.id;
            document.getElementById('modalTitle').textContent = @json(__('Edit Address'));
            document.getElementById('fullName').value = address.full_name;
            document.getElementById('phone').value = address.phone;
            document.getElementById('addressLine1').value = address.address_line1;
            document.getElementById('addressLine2').value = address.address_line2 || '';
            document.getElementById('city').value = address.city;
            document.getElementById('state').value = address.state || '';
            document.getElementById('postalCode').value = address.postal_code;
            document.getElementById('country').value = address.country;
            document.getElementById('isDefault').checked = address.is_default;
            
            document.getElementById('addressModal').classList.add('active');
        }
    }

    async function deleteAddress(id) {
        if (!confirm(@json(__('Are you sure you want to delete this address?')))) return;
        
        try {
            const response = await fetch(`/addresses/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message || window.__t['An error occurred'], 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast(window.__t['An error occurred'], 'error');
        }
    }

    async function setDefaultAddress(id) {
        try {
            const response = await fetch(`/addresses/${id}/set-default`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            });
            
            const data = await response.json();
            
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || window.__t['An error occurred']);
            }
        } catch (error) {
            console.error('Error:', error);
            alert(window.__t['An error occurred']);
        }
    }

    // Close modal on outside click
    document.getElementById('addressModal').addEventListener('click', (e) => {
        if (e.target === e.currentTarget) {
            closeModal();
        }
    });

    // Delete account form
    document.getElementById('deleteAccountForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const errorAlert = document.getElementById('deleteAccountError');
        const successAlert = document.getElementById('deleteAccountSuccess');
        
        errorAlert.style.display = 'none';
        successAlert.style.display = 'none';
        
        if (!confirm(@json(__('Are you sure you want to delete your account? This action cannot be undone!')))) {
            return;
        }
        
        try {
            const response = await fetch('{{ route("profile.destroy-account") }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(formData)),
            });
            
            const data = await response.json();
            
            if (data.success) {
                successAlert.textContent = data.message;
                successAlert.style.display = 'block';
                setTimeout(() => {
                    window.location.href = '{{ route("home") }}';
                }, 2000);
            } else {
                errorAlert.textContent = data.message;
                errorAlert.style.display = 'block';
            }
        } catch (error) {
            console.error('Error:', error);
            errorAlert.textContent = window.__t['An error occurred'];
            errorAlert.style.display = 'block';
        }
    });
</script>
@endsection
