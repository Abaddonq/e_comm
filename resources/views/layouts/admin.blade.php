<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Admin</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Critical CSS - Page Loader Only */
        body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f4f6; }
        .page-loader { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: #f3f4f6; z-index: 9998; display: flex; align-items: center; justify-content: center; }
        .page-loader.hidden { opacity: 0; pointer-events: none; transition: opacity 0.3s; }
        .loader-spinner { width: 40px; height: 40px; border: 3px solid #e5e7eb; border-top: 3px solid #374151; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
    
    <script>
        window.addEventListener('load', function() {
            var loader = document.getElementById('pageLoader');
            if (loader) {
                loader.classList.add('hidden');
                setTimeout(function() { loader.remove(); }, 300);
            }
        });
    </script>
</head>
<body class="font-sans antialiased">
    <!-- Page Loader -->
    <div class="page-loader" id="pageLoader">
        <div class="loader-spinner"></div>
    </div>
    
    <div class="min-h-screen bg-gray-100">
        <nav class="admin-nav">
            <div class="admin-nav-content">
                <div class="admin-nav-left">
                    <a href="{{ route('admin.dashboard') }}" class="admin-logo">
                        DecorMotto
                    </a>
                    <div class="admin-nav-links">
                        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="admin-nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                            Products
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            Categories
                        </a>
                        <a href="{{ route('admin.stock.index') }}" class="admin-nav-link {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}">
                            Stock
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="admin-nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                            Orders
                        </a>
                    </div>
                </div>
                <div class="admin-nav-right">
                    <span class="admin-user-name">{{ auth()->user()->name }}</span>
                    <span style="color: #d1d5db;">|</span>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="admin-logout">
                            Çıkış
                        </button>
                    </form>
                </div>
                <button type="button" class="admin-nav-toggle" id="adminNavToggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="adminMobileMenu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
            <div class="admin-mobile-menu" id="adminMobileMenu">
                <div class="admin-mobile-links">
                    <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('admin.products.index') }}" class="admin-nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">Products</a>
                    <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">Categories</a>
                    <a href="{{ route('admin.stock.index') }}" class="admin-nav-link {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}">Stock</a>
                    <a href="{{ route('admin.orders.index') }}" class="admin-nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">Orders</a>
                    <div class="admin-nav-link" style="justify-content: space-between;">
                        <span>{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="admin-logout">Cikis</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        @if(session('success'))
            <div class="admin-toast admin-toast-success" id="toast-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="admin-toast admin-toast-error" id="toast-error">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="admin-toast admin-toast-error" id="toast-error">
                @foreach($errors->all() as $error)
                    {{ $error }}@if(!$loop->last)<br>@endif
                @endforeach
            </div>
        @endif

        <main class="admin-main">
            <div class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>
    <script>
        (function() {
            var toggle = document.getElementById('adminNavToggle');
            var menu = document.getElementById('adminMobileMenu');

            if (toggle && menu) {
                toggle.addEventListener('click', function() {
                    var isOpen = menu.classList.toggle('open');
                    toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                });

                menu.querySelectorAll('a').forEach(function(link) {
                    link.addEventListener('click', function() {
                        menu.classList.remove('open');
                        toggle.setAttribute('aria-expanded', 'false');
                    });
                });

                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 768) {
                        menu.classList.remove('open');
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                });
            }
        })();

        setTimeout(function() {
            var toastSuccess = document.getElementById('toast-success');
            var toastError = document.getElementById('toast-error');
            if (toastSuccess) toastSuccess.style.display = 'none';
            if (toastError) toastError.style.display = 'none';
        }, 4000);
    </script>
    @stack('scripts')
</body>
</html>
