@extends('layouts.web')

@section('title', ' - Hesabım')

@php
$activeTab = request()->query('tab', 'account');
@endphp

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
        padding: 14px 16px;
        border-radius: 8px;
        color: #444;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s;
        cursor: pointer;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
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
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        padding: 32px;
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
        padding-bottom: 16px;
        border-bottom: 1px solid #eee;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        color: #333;
        margin-bottom: 8px;
    }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        color: #1a1a1a;
        background: white;
        transition: border-color 0.2s;
    }

    .form-input:focus {
        outline: none;
        border-color: #1a1a1a;
    }

    .form-input::placeholder {
        color: #999;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    @media (max-width: 576px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }

    .btn-primary {
        background: #1a1a1a;
        color: white;
    }

    .btn-primary:hover {
        background: #333;
    }

    .btn-secondary {
        background: white;
        color: #1a1a1a;
        border: 1px solid #ddd;
    }

    .btn-secondary:hover {
        border-color: #1a1a1a;
    }

    .btn-danger {
        background: white;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .btn-danger:hover {
        background: #fef2f2;
        border-color: #dc2626;
    }

    .btn-sm {
        padding: 8px 16px;
        font-size: 13px;
    }

    .btn-group {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }

    .alert {
        padding: 14px 16px;
        border-radius: 8px;
        font-size: 14px;
        margin-bottom: 20px;
        display: none;
    }

    .alert-success {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .alert-error {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .orders-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .order-card {
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 20px;
        transition: box-shadow 0.2s;
    }

    .order-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .order-number {
        font-size: 15px;
        font-weight: 600;
        color: #1a1a1a;
    }

    .order-date {
        font-size: 13px;
        color: #666;
    }

    .order-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .order-status.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .order-status.paid, .order-status.processing, .order-status.shipped {
        background: #dbeafe;
        color: #1e40af;
    }

    .order-status.delivered {
        background: #d1fae5;
        color: #065f46;
    }

    .order-status.cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .order-summary {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .order-items-count {
        font-size: 13px;
        color: #666;
    }

    .order-total {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
    }

    .order-actions {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #eee;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state svg {
        width: 64px;
        height: 64px;
        color: #ccc;
        margin-bottom: 16px;
    }

    .empty-state h3 {
        font-size: 18px;
        color: #333;
        margin-bottom: 8px;
    }

    .empty-state p {
        color: #666;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .addresses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    .address-card {
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 20px;
        position: relative;
    }

    .address-card.default {
        border-color: #1a1a1a;
    }

    .address-default-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #1a1a1a;
        color: white;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
    }

    .address-name {
        font-size: 15px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 4px;
    }

    .address-phone {
        font-size: 13px;
        color: #666;
        margin-bottom: 12px;
    }

    .address-text {
        font-size: 14px;
        color: #444;
        line-height: 1.5;
        margin-bottom: 16px;
    }

    .address-actions {
        display: flex;
        gap: 8px;
    }

    .address-actions button, 
    .address-actions a {
        padding: 6px 12px;
        font-size: 12px;
        border-radius: 6px;
        cursor: pointer;
        border: 1px solid #ddd;
        background: white;
        color: #444;
        text-decoration: none;
        transition: all 0.2s;
    }

    .address-actions button:hover,
    .address-actions a:hover {
        border-color: #1a1a1a;
        color: #1a1a1a;
    }

    .address-actions .delete-btn:hover {
        border-color: #dc2626;
        color: #dc2626;
    }

    .add-address-card {
        border: 2px dashed #ddd;
        border-radius: 10px;
        padding: 40px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: none;
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
        display: block;
        font-size: 14px;
        color: #666;
    }

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 20px;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal {
        background: white;
        border-radius: 12px;
        width: 100%;
        max-width: 500px;
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
        color: #1a1a1a;
    }

    .modal-close {
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        color: #666;
    }

    .modal-close:hover {
        color: #1a1a1a;
    }

    .modal-body {
        padding: 24px;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 14px;
        color: #333;
    }

    .checkbox-label input {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .logout-section {
        text-align: center;
        padding: 40px 20px;
    }

    .logout-section p {
        color: #666;
        margin-bottom: 20px;
    }
</style>

<div class="profile-page">
    <div class="profile-container">
        <div class="profile-header">
            <h1>Hesabım</h1>
            <p>Profil bilgilerinizi, siparişlerinizi ve adreslerinizi yönetin</p>
        </div>

        <div class="profile-layout">
            <!-- Sidebar -->
            <div class="profile-sidebar desktop">
                <button class="profile-nav-item {{ $activeTab === 'account' ? 'active' : '' }}" onclick="switchTab('account')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Hesap Bilgileri
                </button>
                <button class="profile-nav-item {{ $activeTab === 'orders' ? 'active' : '' }}" onclick="switchTab('orders')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Siparişlerim
                </button>
                <button class="profile-nav-item {{ $activeTab === 'addresses' ? 'active' : '' }}" onclick="switchTab('addresses')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Adreslerim
                </button>
                <button class="profile-nav-item {{ $activeTab === 'logout' ? 'active' : '' }}" onclick="switchTab('logout')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Çıkış Yap
                </button>
            </div>

            <!-- Mobile Tabs -->
            <div class="profile-sidebar mobile">
                <button class="profile-nav-item {{ $activeTab === 'account' ? 'active' : '' }}" onclick="switchTab('account')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Hesap
                </button>
                <button class="profile-nav-item {{ $activeTab === 'orders' ? 'active' : '' }}" onclick="switchTab('orders')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Siparişler
                </button>
                <button class="profile-nav-item {{ $activeTab === 'addresses' ? 'active' : '' }}" onclick="switchTab('addresses')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Adresler
                </button>
                <button class="profile-nav-item {{ $activeTab === 'logout' ? 'active' : '' }}" onclick="switchTab('logout')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Çıkış
                </button>
            </div>

            <!-- Content -->
            <div class="profile-content">
                <!-- Account Section -->
                <div class="profile-section {{ $activeTab === 'account' ? 'active' : '' }}" id="section-account">
                    <h2 class="section-title">Hesap Bilgileri</h2>
                    
                    <div class="alert alert-success" id="profileAlert"></div>
                    
                    <form id="profileForm">
                        <div class="form-group">
                            <label class="form-label">Ad Soyad</label>
                            <input type="text" name="name" class="form-input" value="{{ $user->name }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">E-posta Adresi</label>
                            <input type="email" name="email" class="form-input" value="{{ $user->email }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Telefon Numarası</label>
                            <input type="tel" name="phone" class="form-input" value="{{ $user->phone ?? '' }}" placeholder="Telefon numaranız">
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">Bilgileri Güncelle</button>
                        </div>
                    </form>

                    <h2 class="section-title" style="margin-top: 48px;">Şifre Değiştir</h2>
                    
                    <div class="alert alert-success" id="passwordAlert"></div>
                    <div class="alert alert-error" id="passwordError"></div>
                    
                    <form id="passwordForm">
                        <div class="form-group">
                            <label class="form-label">Mevcut Şifre</label>
                            <input type="password" name="current_password" class="form-input" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Yeni Şifre</label>
                                <input type="password" name="password" class="form-input" required minlength="8">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Şifre Tekrar</label>
                                <input type="password" name="password_confirmation" class="form-input" required>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">Şifreyi Güncelle</button>
                        </div>
                    </form>
                </div>

                <!-- Orders Section -->
                <div class="profile-section {{ $activeTab === 'orders' ? 'active' : '' }}" id="section-orders">
                    <h2 class="section-title">Siparişlerim</h2>
                    
                    @if($orders->count() > 0)
                        <div class="orders-list">
                            @foreach($orders as $index => $order)
                                <div class="order-card">
                                    <div class="order-header">
                                        <div>
                                            <div class="order-number">Sipariş #{{ $loop->iteration }}</div>
                                            <div class="order-date">{{ $order->created_at->format('d.m.Y') }}</div>
                                        </div>
                                        <span class="order-status {{ $order->status }}">
                                            @switch($order->status)
                                                @case('pending') Beklemede @break
                                                @case('paid') Ödendi @break
                                                @case('processing') İşleniyor @break
                                                @case('shipped') Kargoya Verildi @break
                                                @case('delivered') Teslim Edildi @break
                                                @case('cancelled') İptal Edildi @break
                                                @default {{ $order->status }}
                                            @endswitch
                                        </span>
                                    </div>
                                    <div class="order-summary">
                                        <span class="order-items-count">{{ $order->items->count() }} ürün</span>
                                        <span class="order-total">₺{{ number_format($order->total, 2) }}</span>
                                    </div>
                                    <div class="order-actions">
                                        <button onclick="showOrderDetail({{ $order->id }})" class="btn btn-secondary btn-sm">Sipariş Detayı</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <h3>Henüz Siparişiniz Yok</h3>
                            <p>İlk siparişinizi vermek için alışverişe başlayın</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">Alışverişe Başla</a>
                        </div>
                    @endif
                </div>

                <!-- Addresses Section -->
                <div class="profile-section {{ $activeTab === 'addresses' ? 'active' : '' }}" id="section-addresses">
                    <h2 class="section-title">Adreslerim</h2>
                    
                    <div class="addresses-grid">
                        @foreach($addresses as $address)
                            <div class="address-card {{ $address->is_default ? 'default' : '' }}">
                                @if($address->is_default)
                                    <span class="address-default-badge">Varsayılan</span>
                                @endif
                                <div class="address-name">{{ $address->full_name }}</div>
                                <div class="address-phone">{{ $address->phone }}</div>
                                <div class="address-text">{{ $address->full_address }}</div>
                                <div class="address-actions">
                                    <button onclick="editAddress({{ $address->id }})">Düzenle</button>
                                    @if(!$address->is_default)
                                        <button onclick="setDefaultAddress({{ $address->id }})">Varsayılan Yap</button>
                                    @endif
                                    <button class="delete-btn" onclick="deleteAddress({{ $address->id }})">Sil</button>
                                </div>
                            </div>
                        @endforeach
                        <div class="add-address-card" onclick="openModal()">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            <span>Yeni Adres Ekle</span>
                        </div>
                    </div>
                </div>

                <!-- Order Detail Section -->
                <div class="profile-section" id="section-order-detail">
                    <button onclick="switchTab('orders')" class="btn btn-secondary btn-sm" style="margin-bottom: 20px;">
                        ← Siparişlere Dön
                    </button>
                    <div id="order-detail-content">
                        <!-- Order details will be loaded here -->
                    </div>
                </div>

                <!-- Logout Section -->
                <div class="profile-section {{ $activeTab === 'logout' ? 'active' : '' }}" id="section-logout">
                    <div class="logout-section">
                        <h2 class="section-title">Çıkış Yap</h2>
                        <p>Hesabınızdan çıkış yapmak istediğinizden emin misiniz?</p>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Çıkış yapmak istediğinizden emin misiniz?')">
                                Çıkış Yap
                            </button>
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
            <h3 class="modal-title" id="modalTitle">Yeni Adres Ekle</h3>
            <button class="modal-close" onclick="closeModal()">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <form id="addressForm">
                <input type="hidden" id="addressId">
                <div class="form-group">
                    <label class="form-label">Ad Soyad</label>
                    <input type="text" id="fullName" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Telefon</label>
                    <input type="tel" id="phone" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Adres Satırı 1</label>
                    <input type="text" id="addressLine1" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Adres Satırı 2 (Opsiyonel)</label>
                    <input type="text" id="addressLine2" class="form-input">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Şehir</label>
                        <input type="text" id="city" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">İlçe</label>
                        <input type="text" id="state" class="form-input">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Posta Kodu</label>
                        <input type="text" id="postalCode" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ülke</label>
                        <input type="text" id="country" class="form-input" value="Türkiye" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="isDefault">
                        Bu adresi varsayılan olarak kullan
                    </label>
                </div>
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">İptal</button>
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
                errorAlert.textContent = data.errors?.current_password?.[0] || 'Bir hata oluştu';
                errorAlert.style.display = 'block';
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });

    // Address modal
    function openModal() {
        document.getElementById('addressModal').classList.add('active');
        document.getElementById('modalTitle').textContent = 'Yeni Adres Ekle';
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
                let errorMsg = result.message || 'Bir hata oluştu';
                if (result.errors) {
                    errorMsg = Object.values(result.errors).flat().join('\n');
                }
                alert(errorMsg);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Bir hata oluştu: ' + error.message);
        }
    });

    function editAddress(id) {
        const addresses = @json($addresses);
        const address = addresses.find(a => a.id === id);
        
        if (address) {
            document.getElementById('addressId').value = address.id;
            document.getElementById('modalTitle').textContent = 'Adresi Düzenle';
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
        if (!confirm('Bu adresi silmek istediğinizden emin misiniz?')) return;
        
        try {
            const response = await fetch(`/addresses/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Bir hata oluştu');
                }
            } else {
                alert('Bir hata oluştu: ' + response.status);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Bir hata oluştu');
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
                alert(data.message || 'Bir hata oluştu');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Bir hata oluştu');
        }
    }

    // Close modal on outside click
    document.getElementById('addressModal').addEventListener('click', (e) => {
        if (e.target === e.currentTarget) {
            closeModal();
        }
    });

    // Order Detail
    const ordersData = @json($orders);

    function showOrderDetail(orderId) {
        const order = ordersData.find(o => o.id === orderId);
        if (!order) return;

        // Hide all sections
        document.querySelectorAll('.profile-section').forEach(s => s.classList.remove('active'));
        
        const detailSection = document.getElementById('section-order-detail');
        const content = document.getElementById('order-detail-content');

        const statusLabels = {
            'pending': 'Beklemede',
            'paid': 'Ödendi',
            'processing': 'İşleniyor',
            'shipped': 'Kargoya Verildi',
            'delivered': 'Teslim Edildi',
            'cancelled': 'İptal Edildi'
        };

        let itemsHtml = '';
        if (order.items && order.items.length > 0) {
            order.items.forEach(item => {
                const productName = item.variant?.product?.title || 'Ürün';
                const variantName = item.variant?.sku || '';
                itemsHtml += `
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                        <div>
                            <div style="font-weight: 500;">${productName}</div>
                            <div style="font-size: 13px; color: #666;">${variantName}</div>
                            <div style="font-size: 13px; color: #666;">Adet: ${item.quantity}</div>
                        </div>
                        <div style="font-weight: 600;">₺${parseFloat(item.price * item.quantity).toFixed(2)}</div>
                    </div>
                `;
            });
        }

        content.innerHTML = `
            <h2 class="section-title">Sipariş Detayı</h2>
            <div style="background: #f9f9f9; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                    <span style="color: #666;">Sipariş No:</span>
                    <span style="font-weight: 600;">${order.order_number}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                    <span style="color: #666;">Tarih:</span>
                    <span>${new Date(order.created_at).toLocaleDateString('tr-TR')}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #666;">Durum:</span>
                    <span class="order-status ${order.status}">${statusLabels[order.status] || order.status}</span>
                </div>
            </div>
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 12px;">Ürünler</h3>
            <div style="background: white; padding: 20px; border: 1px solid #eee; border-radius: 10px;">
                ${itemsHtml}
            </div>
            <div style="margin-top: 20px; padding: 20px; background: #f9f9f9; border-radius: 10px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span>Ara Toplam:</span>
                    <span>₺${parseFloat(order.subtotal).toFixed(2)}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span>Kargo:</span>
                    <span>₺${parseFloat(order.shipping_cost).toFixed(2)}</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: 600; padding-top: 12px; border-top: 1px solid #ddd;">
                    <span>Toplam:</span>
                    <span>₺${parseFloat(order.total).toFixed(2)}</span>
                </div>
            </div>
            <div style="margin-top: 20px;">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 12px;">Teslimat Adresi</h3>
                <div style="background: white; padding: 16px; border: 1px solid #eee; border-radius: 10px;">
                    <div style="font-weight: 500;">${order.shipping_name}</div>
                    <div style="color: #666; font-size: 14px;">${order.shipping_phone}</div>
                    <div style="color: #666; font-size: 14px;">${order.shipping_address_line1}</div>
                    ${order.shipping_address_line2 ? '<div style="color: #666; font-size: 14px;">' + order.shipping_address_line2 + '</div>' : ''}
                    <div style="color: #666; font-size: 14px;">${order.shipping_city}, ${order.shipping_state} ${order.shipping_postal_code}</div>
                </div>
            </div>
        `;

        detailSection.classList.add('active');
    }
</script>
