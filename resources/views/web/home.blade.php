@extends('layouts.web')

@section('title', ' - Home')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="hero-bg">
        <img src="https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?w=1920&q=80" alt="Luxury Furniture" style="width:100%;height:100%;object-fit:cover;">
    </div>
    <div class="hero-content">
        <h1 class="hero-title">DecorMotto</h1>
        <p class="hero-subtitle">Ev dekorasyonunda lüksün adresi</p>
        <a href="#products" class="hero-cta">Keşfet</a>
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
        <span class="marquee-item">Ücretsiz Kargo</span>
        <span class="marquee-item">30 Gün İade</span>
        <span class="marquee-item">Taksit Seçenekleri</span>
        <span class="marquee-item">7/24 Destek</span>
        <span class="marquee-item">Ücretsiz Kargo</span>
        <span class="marquee-item">30 Gün İade</span>
        <span class="marquee-item">Taksit Seçenekleri</span>
        <span class="marquee-item">7/24 Destek</span>
    </div>
</div>

<!-- Featured Products -->
<section class="products-section" id="products">
    <div class="section-header">
        <p class="section-title">Koleksiyonlarımız</p>
        <h2 class="section-heading">Öne Çıkan Ürünler</h2>
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
                            <img src="{{ asset('storage/' . $firstImage->path) }}" alt="{{ $product->title }}">
                        @else
                            <img src="https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=600&q=80" alt="{{ $product->title }}">
                        @endif
                        <button class="product-quick-add" data-variant-id="{{ $variantId }}" {{ !$variantId ? 'disabled' : '' }}>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <p class="text-gray-500 text-lg">Henüz ürün bulunmuyor. Yakında döneceğiz!</p>
    </div>
    @endif
</section>

<!-- Categories Section -->
@if($categories->count() > 0)
<section class="products-section" style="background:#fafafa;">
    <div class="section-header">
        <p class="section-title">Kategoriler</p>
        <h2 class="section-heading">İlginizi Çekebilir</h2>
    </div>
    
    <div class="product-grid">
        @foreach($categories as $category)
            <a href="{{ route('category.show', $category->slug) }}" class="product-card" style="text-decoration:none;">
                <div class="product-image">
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                    @else
                        <img src="https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=600&q=80" alt="{{ $category->name }}">
                    @endif
                </div>
                <h3 class="product-name" style="text-align:center;">{{ $category->name }}</h3>
                <p class="product-price" style="text-align:center;font-size:13px;color:var(--color-muted);">{{ $category->products_count ?? 0 }} Ürün</p>
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
