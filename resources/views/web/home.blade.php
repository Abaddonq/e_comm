@extends('layouts.web')

@section('title', ' - Home')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="hero-bg">
        <img 
            src="/img/background.webp" 
            srcset="/img/background.webp 1920w, /img/background.webp 1280w, /img/background.webp 640w"
            sizes="100vw"
            alt="Luxury Furniture" 
            width="1534"
            height="1080"
            style="width:100%;height:100%;object-fit:cover;"
            fetchpriority="high">
    </div>
    <div class="hero-content">
        <h1 class="hero-title">DecorMotto</h1>
        <p class="hero-subtitle">{{ __('The address of luxury in home decoration') }}</p>
        <a href="#products" class="hero-cta">{{ __('Discover') }}</a>
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
                $minPrice = $product->variants->min('price');
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
                        <button class="product-quick-add" data-variant-id="{{ $variantId }}" {{ !$variantId ? 'disabled' : '' }}>
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </button>
                    </div>
                </a>
                <a href="{{ route('product.show', $product->slug) }}">
                    <h3 class="product-name">{{ $product->title }}</h3>
                </a>
                <p class="product-price">₺{{ number_format($minPrice ?? 0, 2) }}</p>
            </div>
        @endforeach
    </div>
    @else
    <div class="text-center" style="padding: 60px 0;">
        <p class="text-gray-500 text-lg">{{ __('No products yet') }}</p>
    </div>
    @endif
</section>

<!-- Categories Section -->
@if($categories->count() > 0)
<section class="products-section" style="background:#fafafa;">
    <div class="section-header">
        <p class="section-title">{{ __('Categories') }}</p>
        <h2 class="section-heading">{{ __('May Interest You') }}</h2>
    </div>
    
    <div class="product-grid">
        @foreach($categories as $category)
            <a href="{{ route('category.show', $category->slug) }}" class="product-card" style="text-decoration:none;">
                <div class="product-image">
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" loading="lazy" decoding="async">
                    @else
                        <img src="/img/mock-img.jpg" alt="{{ $category->name }}" loading="lazy" decoding="async" width="401" height="400">
                    @endif
                </div>
                <h3 class="product-name" style="text-align:center;">{{ $category->name }}</h3>
                <p class="product-price" style="text-align:center;font-size:13px;color:var(--color-muted);">{{ $category->products_count ?? 0 }} {{ __('products_count') }}</p>
            </a>
        @endforeach
    </div>
</section>
@endif
@endsection

@section('scripts')
<script>
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.product-quick-add');
    if (!btn) return;
    
    const variantId = btn.dataset.variantId;
    if (!variantId) return;
    
    e.preventDefault();
    
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            variant_id: variantId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cart-count').textContent = data.cart_count;
            btn.style.background = '#22c55e';
            btn.style.color = 'white';
            setTimeout(() => {
                btn.style.background = '';
                btn.style.color = '';
            }, 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
</script>
@endsection
