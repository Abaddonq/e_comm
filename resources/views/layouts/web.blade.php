<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'DecorMotto') }}@yield('title', '')</title>
    
    @yield('meta_tags')
    
    <meta name="description" content="@yield('meta_description', 'Discover beautiful home decoration products at DecorMotto. Shop now for quality home decor items.')">
    <meta name="author" content="DecorMotto">
    
    @yield('canonical_url')
    
    @yield('open_graph')
    @yield('twitter_card')
    
    <link rel="preload" href="/fonts/inter-v20-latin-regular.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/inter-v20-latin-500.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/inter-v20-latin-600.woff2" as="font" type="font/woff2" crossorigin>
    
    <style>
        @font-face {
            font-family: 'Inter';
            src: url('/fonts/inter-v20-latin-300.woff2') format('woff2');
            font-weight: 300;
            font-display: swap;
        }
        @font-face {
            font-family: 'Inter';
            src: url('/fonts/inter-v20-latin-regular.woff2') format('woff2');
            font-weight: 400;
            font-display: swap;
        }
        @font-face {
            font-family: 'Inter';
            src: url('/fonts/inter-v20-latin-500.woff2') format('woff2');
            font-weight: 500;
            font-display: swap;
        }
        @font-face {
            font-family: 'Inter';
            src: url('/fonts/inter-v20-latin-600.woff2') format('woff2');
            font-weight: 600;
            font-display: swap;
        }
        @font-face {
            font-family: 'Inter';
            src: url('/fonts/inter-v20-latin-700.woff2') format('woff2');
            font-weight: 700;
            font-display: swap;
        }
        
        :root {
            --color-primary: #FFFFFF;
            --color-secondary: #212121;
            --color-accent: #1A1A1A;
            --color-text: #333333;
            --color-hover: #000000;
            --color-muted: #666666;
            --color-border: #E5E5E5;
            
            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            
            --transition-fast: 150ms ease-in-out;
            --transition-base: 300ms ease-in-out;
            --transition-slow: 500ms ease-in-out;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: var(--font-family);
            color: var(--color-text);
            background-color: var(--color-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Search Modal Styles */
        .search-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.6);
            z-index: 2000;
            display: none;
            align-items: flex-start;
            justify-content: center;
            padding-top: 100px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .search-modal.active {
            display: flex;
            opacity: 1;
        }

        .search-modal-content {
            background: white;
            border-radius: 12px;
            width: 100%;
            max-width: 600px;
            max-height: 80vh;
            overflow: hidden;
            transform: translateY(-20px);
            transition: transform 0.2s;
        }

        .search-modal.active .search-modal-content {
            transform: translateY(0);
        }

        .search-modal-header {
            padding: 16px 20px;
            border-bottom: 1px solid #eee;
        }

        .search-input-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f5f5f5;
            border-radius: 8px;
            padding: 12px 16px;
        }

        .search-icon {
            width: 20px;
            height: 20px;
            color: #999;
            flex-shrink: 0;
        }

        .search-input {
            flex: 1;
            border: none;
            background: none;
            font-size: 16px;
            color: #1a1a1a;
            outline: none;
        }

        .search-input::placeholder {
            color: #999;
        }

        .search-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .search-close:hover {
            color: #1a1a1a;
        }

        .search-suggestions {
            max-height: 400px;
            overflow-y: auto;
        }

        .search-loading {
            display: flex;
            justify-content: center;
            padding: 40px;
        }

        .spinner {
            width: 32px;
            height: 32px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #1a1a1a;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .suggestions-list {
            padding: 8px 0;
        }

        .suggestion-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            color: inherit;
        }

        .suggestion-item:hover {
            background: #f9f9f9;
        }

        .suggestion-image {
            width: 48px;
            height: 48px;
            border-radius: 6px;
            object-fit: cover;
            background: #f5f5f5;
        }

        .suggestion-info {
            flex: 1;
        }

        .suggestion-title {
            font-size: 14px;
            font-weight: 500;
            color: #1a1a1a;
        }

        .suggestion-price {
            font-size: 13px;
            color: #666;
            margin-top: 2px;
        }

        .recent-searches {
            padding: 16px 20px;
            border-top: 1px solid #eee;
        }

        .recent-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .recent-header span {
            font-size: 12px;
            font-weight: 500;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .recent-header button {
            background: none;
            border: none;
            font-size: 12px;
            color: #666;
            cursor: pointer;
        }

        .recent-header button:hover {
            color: #1a1a1a;
        }

        .recent-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .recent-item {
            background: #f5f5f5;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            color: #444;
            cursor: pointer;
            transition: background 0.2s;
        }

        .recent-item:hover {
            background: #eee;
        }
        
        /* Header Styles */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 85px;
            z-index: 1000;
            transition: all var(--transition-base);
            background: transparent;
        }
        
        .header.scrolled {
            background: var(--color-primary);
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
        }
        
        .header.scrolled .nav-link,
        .header.scrolled .header-icon {
            color: var(--color-secondary);
        }
        
        .header.scrolled .logo {
            color: var(--color-secondary);
        }
        
        .header-inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .logo {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.2em;
            color: var(--color-primary);
            text-decoration: none;
            transition: color var(--transition-base);
        }
        
        .nav-left, .nav-right {
            display: flex;
            align-items: center;
            gap: 32px;
        }
        
        .nav-link {
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--color-primary);
            text-decoration: none;
            transition: color var(--transition-base);
            position: relative;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--color-primary);
            transition: width var(--transition-base);
        }
        
        .header.scrolled .nav-link::after {
            background: var(--color-secondary);
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        
        .header-icons {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        
        .header-icon {
            color: #ffffff;
            cursor: pointer;
            transition: color var(--transition-base);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            padding: 0;
            width: 44px;
            height: 44px;
            min-width: 44px;
            min-height: 44px;
        }
        
        .header-icon:focus {
            outline: none;
            box-shadow: none;
        }
        
        .header-icon svg {
            width: 26px;
            height: 27px;
        }
        
        .header-icon:hover {
            color: var(--color-hover);
        }
        
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--color-accent);
            color: white;
            font-size: 10px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Hero Section */
        .hero {
            height: 100vh;
            width: 100%;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1a1a1a 0%, #333333 100%);
        }
        
        .hero-bg::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50%;
            background: linear-gradient(to top, rgba(0,0,0,0.5), transparent);
        }
        
        .hero-content {
            position: relative;
            z-index: 10;
            text-align: center;
            color: white;
            animation: fadeIn 1s ease-out;
        }
        
        .hero-title {
            font-size: 72px;
            font-weight: 300;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin-bottom: 24px;
        }
        
        .hero-subtitle {
            font-size: 16px;
            font-weight: 300;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            margin-bottom: 48px;
            opacity: 0.8;
        }
        
        .hero-cta {
            display: inline-block;
            padding: 16px 48px;
            border: 1px solid white;
            color: white;
            font-size: 13px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            text-decoration: none;
            transition: all var(--transition-base);
        }
        
        .hero-cta:hover {
            background: white;
            color: var(--color-secondary);
        }
        
        .scroll-indicator {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            animation: bounce 2s infinite;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
            40% { transform: translateX(-50%) translateY(-10px); }
            60% { transform: translateX(-50%) translateY(-5px); }
        }
        
        /* Marquee */
        .marquee {
            background: var(--color-secondary);
            color: white;
            padding: 16px 0;
            overflow: hidden;
            white-space: nowrap;
        }
        
        .marquee-inner {
            display: inline-block;
            animation: marquee 30s linear infinite;
        }
        
        .marquee-item {
            display: inline-block;
            padding: 0 48px;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.2em;
            text-transform: uppercase;
        }
        
        .marquee-item::after {
            content: '•';
            margin-left: 48px;
            opacity: 0.5;
        }
        
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        
        /* Product Grid */
        .products-section {
            padding: 100px 40px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            color: var(--color-muted);
            margin-bottom: 16px;
        }
        
        .section-heading {
            font-size: 42px;
            font-weight: 300;
            letter-spacing: 0.05em;
            color: var(--color-secondary);
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 32px;
        }
        
        @media (max-width: 1200px) {
            .product-grid { grid-template-columns: repeat(3, 1fr); }
        }
        
        @media (max-width: 900px) {
            .product-grid { grid-template-columns: repeat(2, 1fr); }
        }
        
        @media (max-width: 640px) {
            .product-grid { grid-template-columns: repeat(2, 1fr); gap: 16px; }
            
            /* Hero Mobile */
            .hero-title { font-size: 36px; }
            .hero-subtitle { font-size: 12px; letter-spacing: 0.2em; }
            .hero-cta { padding: 14px 32px; font-size: 12px; }
            
            /* Header Mobile */
            .header { height: 70px; }
            .header-inner { padding: 0 20px; }
            .logo { font-size: 18px; }
            
            /* Touch-friendly buttons */
            .quick-add-btn, .wishlist-btn, .hero-cta {
                min-height: 44px;
                min-width: 44px;
            }
        }
        
        /* Smooth scroll snap for sections */
        html {
            scroll-behavior: smooth;
            scroll-snap-type: y proximity;
        }
        
        .product-card {
            position: relative;
            cursor: pointer;
        }
        
        .product-image {
            position: relative;
            aspect-ratio: 1;
            overflow: hidden;
            background: #f5f5f5;
            margin-bottom: 16px;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform var(--transition-slow);
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.05);
        }
        
        .product-quick-add {
            position: absolute;
            bottom: 16px;
            right: 16px;
            width: 48px;
            height: 48px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transform: translateY(10px);
            transition: all var(--transition-base);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            cursor: pointer;
            border: none;
        }
        
        .product-card:hover .product-quick-add {
            opacity: 1;
            transform: translateY(0);
        }
        
        .product-quick-add:hover {
            background: var(--color-secondary);
            color: white;
        }
        
        .product-name {
            font-size: 14px;
            font-weight: 500;
            color: var(--color-secondary);
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .product-price {
            font-size: 15px;
            font-weight: 600;
            color: var(--color-secondary);
        }
        
        /* Footer */
        .footer {
            background: var(--color-secondary);
            color: white;
            padding: 80px 40px 40px;
        }
        
        .footer-inner {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 60px;
            margin-bottom: 60px;
        }
        
        @media (max-width: 900px) {
            .footer-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 40px;
            }
        }
        
        .footer-brand {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.2em;
            margin-bottom: 24px;
        }
        
        .footer-desc {
            font-size: 14px;
            color: rgba(255,255,255,0.6);
            line-height: 1.8;
            margin-bottom: 24px;
        }
        
        .footer-title {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            margin-bottom: 24px;
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 12px;
        }
        
        .footer-links a {
            font-size: 14px;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            transition: color var(--transition-fast);
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .newsletter-form {
            display: flex;
            gap: 0;
        }
        
        .newsletter-input {
            flex: 1;
            padding: 14px 16px;
            border: none;
            background: rgba(255,255,255,0.1);
            color: white;
            font-size: 14px;
        }
        
        .newsletter-input::placeholder {
            color: rgba(255,255,255,0.4);
        }
        
        .newsletter-btn {
            padding: 14px 24px;
            background: white;
            color: var(--color-secondary);
            border: none;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            cursor: pointer;
            transition: background var(--transition-fast);
        }
        
        .newsletter-btn:hover {
            background: var(--color-muted);
        }
        
        .footer-bottom {
            padding-top: 40px;
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: rgba(255,255,255,0.4);
        }
        
        .social-links {
            display: flex;
            gap: 20px;
        }
        
        .social-link {
            color: rgba(255,255,255,0.4);
            transition: color var(--transition-fast);
        }
        
        .social-link:hover {
            color: white;
        }
        
        /* Utility Classes */
        .hidden { display: none !important; }
        
        @media (max-width: 1024px) {
            .nav-left, .nav-right { display: none; }
            .mobile-menu-btn { display: flex !important; }
        }
        
        .mobile-menu-btn {
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            width: 44px;
            height: 44px;
            min-width: 44px;
            min-height: 44px;
        }
        
        .mobile-menu-btn span {
            width: 24px;
            height: 2px;
            background: var(--color-primary);
            transition: all var(--transition-base);
        }
        
        .header.scrolled .mobile-menu-btn span {
            background: var(--color-secondary);
        }

        /* Toast Notification */
        .toast-notification {
            position: fixed;
            top: 100px;
            right: 24px;
            z-index: 9999;
            padding: 16px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            animation: toastSlideIn 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .toast-notification.toast-success {
            background: #1a1a1a;
            color: white;
        }

        .toast-notification.toast-error {
            background: #dc2626;
            color: white;
        }

        @keyframes toastSlideIn {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
    
    @yield('schema')
</head>
<body>
    <!-- Header -->
    <header class="header" id="header">
        <div class="header-inner">
            <nav class="nav-left">
                <a href="#" class="nav-link">Berjer</a>
                <a href="#" class="nav-link">Sehpa</a>
                <a href="#" class="nav-link">Puf</a>
                <a href="#" class="nav-link">Kırlent</a>
                <a href="#" class="nav-link">Aksesuar</a>
                <a href="#" class="nav-link">Tablo</a>
                <a href="#" class="nav-link">Sandalye</a>
            </nav>
            
            <a href="{{ route('home') }}" class="logo">DECORMOTTO</a>
            
            <nav class="nav-right">
                <a href="#" class="nav-link">Etnik</a>
                <a href="#" class="nav-link">Vip</a>
                <a href="#" class="nav-link">Hikayemiz</a>
                <a href="#" class="nav-link">İletişim</a>
                <a href="#" class="nav-link">Mağazalar</a>
                <a href="#" class="nav-link">Blog</a>
            </nav>
            
            <div class="header-icons">
                <button type="button" class="header-icon" id="searchOpenBtn" aria-label="Ara">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
                <div class="header-user-icon" style="position: relative;">
                    <a href="{{ auth()->check() ? route('profile.index') : route('login') }}" class="header-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </a>
                    <div class="user-dropdown" style="position: absolute; top: 100%; right: 0; background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.15); border-radius: 8px; min-width: 150px; display: none; z-index: 100;">
                        @auth
                            <span style="display: block; padding: 12px 16px; color: #666; font-size: 12px; border-bottom: 1px solid #eee;">{{ auth()->user()->name }}</span>
                            <a href="{{ route('profile.index') }}" style="display: block; padding: 12px 16px; color: #333; text-decoration: none; font-size: 14px; border-bottom: 1px solid #eee;">Hesabım</a>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="display: block; padding: 12px 16px; color: #333; text-decoration: none; font-size: 14px;">Çıkış Yap</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @else
                            <a href="{{ route('login') }}" style="display: block; padding: 12px 16px; color: #333; text-decoration: none; font-size: 14px; border-bottom: 1px solid #eee;">Giriş Yap</a>
                            <a href="{{ route('register') }}" style="display: block; padding: 12px 16px; color: #333; text-decoration: none; font-size: 14px;">Üye Ol</a>
                        @endauth
                    </div>
                </div>
                <a href="{{ route('cart.index') }}" class="header-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span class="cart-badge" id="cart-count">{{ $cartCount }}</span>
                </a>
                <div class="mobile-menu-btn">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </header>

    <!-- Search Modal -->
    <div class="search-modal" id="searchModal">
        <div class="search-modal-content">
            <div class="search-modal-header">
                <div class="search-input-wrapper">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="searchInput" class="search-input" placeholder="Ürün ara..." autocomplete="off">
                    <button type="button" class="search-close" id="searchCloseBtn">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="search-suggestions" id="searchSuggestions">
                <div class="search-loading" id="searchLoading" style="display: none;">
                    <div class="spinner"></div>
                </div>
                <div class="suggestions-list" id="suggestionsList"></div>
                <div class="recent-searches" id="recentSearches" style="display: none;">
                    <div class="recent-header">
                        <span>Son Aramalar</span>
                        <button type="button" id="clearRecentSearches">Temizle</button>
                    </div>
                    <div class="recent-list" id="recentList"></div>
                </div>
            </div>
        </div>
    </div>

    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-grid">
                <div>
                    <div class="footer-brand">DECORMOTTO</div>
                    <p class="footer-desc">Ev dekorasyonunda lüks ve zarafeti bir araya getiren koleksiyonlarımızla yaşam alanlarınızı yeniden tanımlıyoruz.</p>
                    <form class="newsletter-form">
                        <input type="email" class="newsletter-input" placeholder="E-posta adresiniz">
                        <button type="submit" class="newsletter-btn">Abone Ol</button>
                    </form>
                </div>
                <div>
                    <h4 class="footer-title">Kategoriler</h4>
                    <ul class="footer-links">
                        <li><a href="#">Berjer</a></li>
                        <li><a href="#">Sehpa</a></li>
                        <li><a href="#">Puf</a></li>
                        <li><a href="#">Kırlent</a></li>
                        <li><a href="#">Aksesuar</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-title">Bilgi</h4>
                    <ul class="footer-links">
                        <li><a href="#">Hakkımızda</a></li>
                        <li><a href="#">İletişim</a></li>
                        <li><a href="#">Mağazalar</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Kariyer</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-title">Müşteri Hizmetleri</h4>
                    <ul class="footer-links">
                        <li><a href="#">Teslimat</a></li>
                        <li><a href="#">İade</a></li>
                        <li><a href="#">Gizlilik</a></li>
                        <li><a href="#">Kullanım Şartları</a></li>
                        <li><a href="#">SSS</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} DecorMotto. Tüm hakları saklıdır.</span>
                <div class="social-links">
                    <a href="#" class="social-link">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                    </a>
                    <a href="#" class="social-link">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <a href="#" class="social-link">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Toast notification function
        function showToast(message, type = 'success') {
            const existing = document.querySelector('.toast-notification');
            if (existing) existing.remove();

            const toast = document.createElement('div');
            toast.className = `toast-notification toast-${type}`;
            toast.innerHTML = `
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' 
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'}
                </svg>
                ${message}
            `;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.animation = 'toastSlideIn 0.3s ease reverse';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Wishlist function
        function toggleWishlist(productId, event) {
            event.preventDefault();
            event.stopPropagation();

            const btn = document.getElementById('wishlist-btn-' + productId);

            fetch('{{ route("wishlist.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (btn) {
                        btn.classList.toggle('active');
                    }
                    showToast(data.is_added ? 'Ürün favorilere eklendi' : 'Ürün favorilerden kaldırıldı', 'success');
                } else if (data.error) {
                    showToast(data.error, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Bir hata oluştu', 'error');
            });
        }

        // Header scroll effect
        const header = document.getElementById('header');
        const heroSection = document.querySelector('.hero');
        
        // If no hero section (not homepage), always show header as scrolled
        if (!heroSection) {
            header.classList.add('scrolled');
        }
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else if (heroSection) {
                header.classList.remove('scrolled');
            }
        });
        
        // User dropdown hover effect
        const userIcon = document.querySelector('.header-user-icon');
        const userDropdown = document.querySelector('.user-dropdown');
        
        if (userIcon && userDropdown) {
            userIcon.addEventListener('mouseenter', () => {
                userDropdown.style.display = 'block';
            });
            userIcon.addEventListener('mouseleave', () => {
                userDropdown.style.display = 'none';
            });
        }

        // Search Modal
        const searchModal = document.getElementById('searchModal');
        const searchOpenBtn = document.getElementById('searchOpenBtn');
        const searchCloseBtn = document.getElementById('searchCloseBtn');
        const searchInput = document.getElementById('searchInput');
        const suggestionsList = document.getElementById('suggestionsList');
        const searchLoading = document.getElementById('searchLoading');
        const recentSearches = document.getElementById('recentSearches');
        const recentList = document.getElementById('recentList');
        const clearRecentSearches = document.getElementById('clearRecentSearches');

        const RECENT_SEARCHES_KEY = 'recentSearches';
        const MAX_RECENT = 5;

        function getRecentSearches() {
            try {
                return JSON.parse(localStorage.getItem(RECENT_SEARCHES_KEY)) || [];
            } catch {
                return [];
            }
        }

        function saveRecentSearch(query) {
            if (!query.trim()) return;
            let recent = getRecentSearches();
            recent = recent.filter(s => s.toLowerCase() !== query.toLowerCase());
            recent.unshift(query);
            recent = recent.slice(0, MAX_RECENT);
            localStorage.setItem(RECENT_SEARCHES_KEY, JSON.stringify(recent));
            showRecentSearches();
        }

        function clearRecentSearchesList() {
            localStorage.removeItem(RECENT_SEARCHES_KEY);
            showRecentSearches();
        }

        function showRecentSearches() {
            const recent = getRecentSearches();
            if (recent.length > 0) {
                recentSearches.style.display = 'block';
                recentList.innerHTML = recent.map(s => 
                    `<span class="recent-item" onclick="document.getElementById('searchInput').value='${s.replace(/'/g, "\\'")}';performSearch();">${s}</span>`
                ).join('');
            } else {
                recentSearches.style.display = 'none';
            }
        }

        function openSearchModal() {
            searchModal.classList.add('active');
            document.body.style.overflow = 'hidden';
            setTimeout(() => searchInput.focus(), 100);
            showRecentSearches();
            suggestionsList.innerHTML = '';
        }

        function closeSearchModal() {
            searchModal.classList.remove('active');
            document.body.style.overflow = '';
            searchInput.value = '';
            suggestionsList.innerHTML = '';
            recentSearches.style.display = 'none';
        }

        if (searchOpenBtn) {
            searchOpenBtn.addEventListener('click', openSearchModal);
        }
        
        if (searchCloseBtn) {
            searchCloseBtn.addEventListener('click', closeSearchModal);
        }

        if (searchModal) {
            searchModal.addEventListener('click', (e) => {
                if (e.target === searchModal) {
                    closeSearchModal();
                }
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && searchModal.classList.contains('active')) {
                closeSearchModal();
            }
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                openSearchModal();
            }
        });

        let searchTimeout;
        function performSearch() {
            const query = searchInput.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                suggestionsList.innerHTML = '';
                recentSearches.style.display = getRecentSearches().length > 0 ? 'block' : 'none';
                return;
            }

            recentSearches.style.display = 'none';
            searchLoading.style.display = 'flex';
            suggestionsList.innerHTML = '';

            searchTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(`/search/suggestions?q=${encodeURIComponent(query)}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    const data = await response.json();
                    searchLoading.style.display = 'none';
                    
                    if (data.products && data.products.length > 0) {
                        suggestionsList.innerHTML = data.products.map(p => `
                            <a href="/products/${p.slug}" class="suggestion-item">
                                ${p.image ? `<img src="${p.image}" alt="${p.title}" class="suggestion-image">` : '<div class="suggestion-image"></div>'}
                                <div class="suggestion-info">
                                    <div class="suggestion-title">${p.title}</div>
                                    ${p.price ? `<div class="suggestion-price">₺${p.price}</div>` : ''}
                                </div>
                            </a>
                        `).join('');
                    } else {
                        suggestionsList.innerHTML = `
                            <div style="padding: 20px; text-align: center; color: #666;">
                                "${query}" için sonuç bulunamadı
                            </div>
                        `;
                    }
                } catch (error) {
                    console.error('Search error:', error);
                    searchLoading.style.display = 'none';
                }
            }, 300);
        }

        if (searchInput) {
            searchInput.addEventListener('input', performSearch);
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && searchInput.value.trim()) {
                    saveRecentSearch(searchInput.value.trim());
                    window.location.href = `/search?q=${encodeURIComponent(searchInput.value.trim())}`;
                }
            });
        }

        if (clearRecentSearches) {
            clearRecentSearches.addEventListener('click', clearRecentSearchesList);
        }
    </script>
    
    @yield('scripts')
</body>
</html>
