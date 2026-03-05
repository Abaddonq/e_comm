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
                <button class="profile-nav-item {{ $activeTab === 'wishlist' ? 'active' : '' }}" onclick="switchTab('wishlist')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    Favorilerim
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
                <button class="profile-nav-item {{ $activeTab === 'wishlist' ? 'active' : '' }}" onclick="switchTab('wishlist')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    Favoriler
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
                                        <span class="order-items-count">{{ $order->items->count() }} ürün</span>
                                        <span class="order-total">₺{{ number_format($order->total, 2) }}</span>
                                    </div>
                                    <div class="order-actions">
                                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-secondary btn-sm">Sipariş Detayı</a>
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

                <!-- Wishlist Section -->
                <div class="profile-section {{ $activeTab === 'wishlist' ? 'active' : '' }}" id="section-wishlist">
                    <h2 class="section-title">Favorilerim</h2>
                    
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
                                            <div class="no-image">Görsel Yok</div>
                                        @endif
                                    </a>
                                    <div class="wishlist-info">
                                        <h3><a href="{{ route('product.show', $product->slug) }}">{{ $product->title }}</a></h3>
                                        <div class="wishlist-price">₺{{ number_format($minPrice, 2) }}</div>
                                        <div class="wishlist-actions">
                                            <button class="btn btn-sm btn-primary" onclick="quickAdd({{ $product->id }}, event)">Sepete Ekle</button>
                                            <button class="btn btn-sm btn-secondary" onclick="removeFromWishlist({{ $product->id }})">Kaldır</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-wishlist">
                                <svg width="64" height="64" fill="none" stroke="#ccc" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                <h3>Henüz Favoriniz Yok</h3>
                                <p>Beğendiğiniz ürünleri favorilere ekleyin</p>
                                <a href="{{ route('home') }}" class="btn btn-primary">Alışverişe Başla</a>
                            </div>
                        @endif
                    </div>
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
                    
                    <div class="delete-account-section">
                        <h2 class="section-title">Hesabı Sil</h2>
                        <p class="warning-text">Hesabınızı sildiğinizde tüm verileriniz kalıcı olarak silinecektir. Bu işlem geri alınamaz.</p>
                        
                        <div class="alert alert-error" id="deleteAccountError" style="display: none;"></div>
                        <div class="alert alert-success" id="deleteAccountSuccess" style="display: none;"></div>
                        
                        <form id="deleteAccountForm">
                            <div class="form-group">
                                <label class="form-label">Şifrenizi doğrulayın</label>
                                <input type="password" name="password" class="form-input" placeholder="Şifreniz" required>
                            </div>
                            <div class="btn-group">
                                <button type="submit" class="btn btn-danger">Hesabımı Sil</button>
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

    function removeFromWishlist(productId) {
        fetch('{{ route("wishlist.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ 
                product_id: wishlistId 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Ürün favorilerden kaldırıldı', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Bir hata oluştu', 'error');
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
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message || 'Bir hata oluştu', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Bir hata oluştu', 'error');
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

    // Delete account form
    document.getElementById('deleteAccountForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const errorAlert = document.getElementById('deleteAccountError');
        const successAlert = document.getElementById('deleteAccountSuccess');
        
        errorAlert.style.display = 'none';
        successAlert.style.display = 'none';
        
        if (!confirm('Hesabınızı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!')) {
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
            errorAlert.textContent = 'Bir hata oluştu';
            errorAlert.style.display = 'block';
        }
    });
</script>
