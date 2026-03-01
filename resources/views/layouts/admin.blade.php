<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .admin-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            width: 100%;
            height: 64px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .admin-nav-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            padding: 0 24px;
            max-width: 100%;
        }
        .admin-nav-left {
            display: flex;
            align-items: center;
            gap: 32px;
        }
        .admin-logo {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            text-decoration: none;
        }
        .admin-logo:hover {
            color: #111827;
        }
        .admin-nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .admin-nav-link {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: 500;
            color: #6b7280;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.15s ease;
        }
        .admin-nav-link:hover {
            color: #374151;
            background: #f9fafb;
        }
        .admin-nav-link.active {
            color: #4f46e5;
            background: #eef2ff;
        }
        .admin-nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .admin-user-name {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }
        .admin-logout {
            font-size: 14px;
            color: #6b7280;
            text-decoration: none;
            cursor: pointer;
        }
        .admin-logout:hover {
            color: #111827;
        }
        .admin-main {
            padding-top: 64px;
        }
    </style>
</head>
<body class="font-sans antialiased">
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
            </div>
        </nav>

        <main class="admin-main">
            <div class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
