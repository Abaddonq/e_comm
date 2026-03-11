@extends('layouts.web')

@section('title', ' - Home')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="hero-bg">
        <picture>
            <source
                type="image/avif"
                srcset="/img/background.avif 1920w, /img/background.avif 1280w, /img/background.avif 640w"
                sizes="100vw"
            >
            <source
                type="image/webp"
                srcset="/img/background.webp 1920w, /img/background.webp 1280w, /img/background.webp 640w"
                sizes="100vw"
            >
            <img
                class="hero-bg-image"
                src="/img/background.webp"
                srcset="/img/background.webp 1920w, /img/background.webp 1280w, /img/background.webp 640w"
                sizes="100vw"
                alt="Luxury Furniture"
                width="1534"
                height="1080"
                decoding="async"
                fetchpriority="high"
            >
        </picture>
    </div>
    <div class="hero-content">
        <h1 class="hero-title">DecorMotto</h1>
        <p class="hero-subtitle">{{ __('The address of luxury in home decoration') }}</p>
        <div class="hero-actions">
            <a href="#products" class="hero-cta">{{ __('Discover') }}</a>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="hero-cta hero-cta-admin">{{ __('Admin Panel') }}</a>
                @endif
            @endauth
        </div>
    </div>
    <div class="scroll-indicator">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
        </svg>
    </div>
</section>

<!-- Marquee -->
<div class="marquee">
    <div class="marquee-inner">
        <span class="marquee-item">{{ __('Free Shipping') }}</span>
        <span class="marquee-item">{{ __('30 Day Returns') }}</span>
        <span class="marquee-item">{{ __('Installment Options') }}</span>
        <span class="marquee-item">{{ __('24/7 Support') }}</span>
        <span class="marquee-item">{{ __('Free Shipping') }}</span>
        <span class="marquee-item">{{ __('30 Day Returns') }}</span>
        <span class="marquee-item">{{ __('Installment Options') }}</span>
        <span class="marquee-item">{{ __('24/7 Support') }}</span>
    </div>
</div>

<!-- Featured Products -->
<section class="products-section" id="products">
    <div class="section-header">
        <p class="section-title">{{ __('Our Collections') }}</p>
        <h2 class="section-heading">{{ __('Featured Products') }}</h2>
    </div>
    
    @if($featuredProducts->count() > 0)
    <div class="product-grid">
        @foreach($featuredProducts as $product)
            @php
                $firstVariant = $product->variants->first();
                $firstImage = $product->images->first();
                $variantId = $firstVariant ? $firstVariant->id : 0;
            @endphp
            <div class="product-card">
                <a href="{{ route('product.show', $product->slug) }}">
                    <div class="product-image">
                        @if($firstImage)
                            <img src="{{ asset('storage/' . $firstImage->path) }}" alt="{{ $product->title }}" loading="lazy" decoding="async">
                        @else
                            <img src="/img/mock-img.jpg" alt="{{ $product->title }}" loading="lazy" decoding="async" width="401" height="400">
                        @endif
                        <button type="button" class="product-quick-add" data-variant-id="{{ $variantId }}" aria-label="{{ __('Add to Cart') }}" title="{{ __('Add to Cart') }}" {{ !$variantId ? 'disabled' : '' }}>
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </button>
                    </div>
                </a>
                <a href="{{ route('product.show', $product->slug) }}">
                    <h3 class="product-name">{{ $product->title }}</h3>
                </a>
                <p class="product-price">₺{{ number_format($product->variants_min_price ?? 0, 2) }}</p>
            </div>
        @endforeach
    </div>
    @else
    <div class="text-center home-empty-state">
        <p class="text-gray-500 text-lg">{{ __('No products yet') }}</p>
    </div>
    @endif
</section>

<!-- Categories Section -->
@if($categories->count() > 0)
<section class="products-section home-categories-section">
    <div class="section-header">
        <p class="section-title">{{ __('Categories') }}</p>
        <h2 class="section-heading">{{ __('May Interest You') }}</h2>
    </div>
    
    <div class="product-grid">
        @foreach($categories as $category)
            <a href="{{ route('category.show', $category->slug) }}" class="product-card home-category-card">
                <div class="product-image">
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" loading="lazy" decoding="async">
                    @else
                        <img src="/img/mock-img.jpg" alt="{{ $category->name }}" loading="lazy" decoding="async" width="401" height="400">
                    @endif
                </div>
                <h3 class="product-name home-category-name">{{ $category->name }}</h3>
                <p class="product-price home-category-count">{{ $category->products_count ?? 0 }} {{ __('products_count') }}</p>
            </a>
        @endforeach
    </div>
</section>
@endif
@endsection

