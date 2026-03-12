@extends('layouts.web')

@section('title', ' - ' . __('My Account'))

@section('content')
<div
    class="profile-page"
    data-active-tab="{{ $activeTab }}"
    data-profile-update-url="{{ route('profile.update-profile') }}"
    data-password-update-url="{{ route('profile.update-password') }}"
    data-destroy-account-url="{{ route('profile.destroy-account') }}"
    data-address-store-url="{{ route('addresses.store') }}"
    data-home-url="{{ route('home') }}"
    data-wishlist-toggle-url="{{ route('wishlist.toggle') }}"
    data-profile-tab-content-url="{{ route('profile.tab-content', ['tab' => '__TAB__']) }}"
    data-addresses='@json($addresses)'
    data-new-address-label="{{ __('New Address') }}"
    data-edit-address-label="{{ __('Edit Address') }}"
    data-confirm-delete-address="{{ __('Are you sure you want to delete this address?') }}"
    data-confirm-delete-account="{{ __('Are you sure you want to delete your account? This action cannot be undone!') }}"
>
    <div class="profile-container">
        <div class="profile-header">
            <h1>{{ __('My Account') }}</h1>
            <p>{{ __('Manage your profile, orders and addresses') }}</p>
        </div>

        <div class="profile-layout">
            <!-- Sidebar -->
            <div class="profile-sidebar desktop">
                <button class="profile-nav-item {{ $activeTab === 'account' ? 'active' : '' }}" data-tab="account" onclick="switchTab('account')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ __('Account Information') }}
                </button>
                <button class="profile-nav-item {{ $activeTab === 'orders' ? 'active' : '' }}" data-tab="orders" onclick="switchTab('orders')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    {{ __('My Orders') }}
                </button>
                <button class="profile-nav-item {{ $activeTab === 'wishlist' ? 'active' : '' }}" data-tab="wishlist" onclick="switchTab('wishlist')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    {{ __('My Wishlist') }}
                </button>
                <button class="profile-nav-item {{ $activeTab === 'addresses' ? 'active' : '' }}" data-tab="addresses" onclick="switchTab('addresses')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ __('My Addresses') }}
                </button>
                <button class="profile-nav-item {{ $activeTab === 'logout' ? 'active' : '' }}" data-tab="logout" onclick="switchTab('logout')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    {{ __('Logout') }}
                </button>
            </div>

            <!-- Mobile Tabs -->
            <div class="profile-sidebar mobile">
                <button class="profile-nav-item {{ $activeTab === 'account' ? 'active' : '' }}" data-tab="account" onclick="switchTab('account')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ __('Account') }}
                </button>
                <button class="profile-nav-item {{ $activeTab === 'orders' ? 'active' : '' }}" data-tab="orders" onclick="switchTab('orders')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    {{ __('Orders') }}
                </button>
                <button class="profile-nav-item {{ $activeTab === 'wishlist' ? 'active' : '' }}" data-tab="wishlist" onclick="switchTab('wishlist')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    {{ __('Favorites') }}
                </button>
                <button class="profile-nav-item {{ $activeTab === 'addresses' ? 'active' : '' }}" data-tab="addresses" onclick="switchTab('addresses')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ __('Addresses') }}
                </button>
                <button class="profile-nav-item {{ $activeTab === 'logout' ? 'active' : '' }}" data-tab="logout" onclick="switchTab('logout')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    {{ __('Logout') }}
                </button>
            </div>

            <!-- Content -->
            <div class="profile-content">
                <!-- Account Section -->
                <div class="profile-section {{ $activeTab === 'account' ? 'active' : '' }}" id="section-account">
                    <h2 class="section-title">{{ __('Account Information') }}</h2>
                    
                    <div class="alert alert-success hidden-alert" id="profileAlert"></div>
                    
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

                    <h2 class="section-title section-title-spaced">{{ __('Change Password') }}</h2>
                    
                    <div class="alert alert-success hidden-alert" id="passwordAlert"></div>
                    <div class="alert alert-error hidden-alert" id="passwordError"></div>
                    
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

                    <div id="ordersTabContent" data-tab-content="orders" data-loaded="{{ $activeTab === 'orders' ? 'true' : 'false' }}">
                        @if($activeTab === 'orders')
                            @include('web.profile.partials.orders-tab', ['orders' => $orders])
                        @else
                            <div class="empty-state"><p>{{ __('Loading orders...') }}</p></div>
                        @endif
                    </div>
                </div>

                <!-- Wishlist Section -->
                <div class="profile-section {{ $activeTab === 'wishlist' ? 'active' : '' }}" id="section-wishlist">
                    <h2 class="section-title">{{ __('My Wishlist') }}</h2>

                    <div id="wishlistTabContent" data-tab-content="wishlist" data-loaded="{{ $activeTab === 'wishlist' ? 'true' : 'false' }}">
                        @if($activeTab === 'wishlist')
                            @include('web.profile.partials.wishlist-tab', ['wishlistProducts' => $wishlistProducts])
                        @else
                            <div class="empty-wishlist"><p>{{ __('Loading wishlist...') }}</p></div>
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
                        
                        <div class="alert alert-error hidden-alert" id="deleteAccountError"></div>
                        <div class="alert alert-success hidden-alert" id="deleteAccountSuccess"></div>
                        
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


@endsection
