@extends('layouts.web')

@section('title', ' - ' . $product->title)

@section('meta_description', $product->meta_description ?? strip_tags($product->description))

@section('canonical_url')
<link rel="canonical" href="{{ route('product.show', $product->slug) }}">
@endsection

@section('open_graph')
<meta property="og:type" content="product">
<meta property="og:title" content="{{ $product->meta_title ?? $product->title }} - {{ config('app.name') }}">
<meta property="og:description" content="{{ $product->meta_description ?? strip_tags($product->description) }}">
<meta property="og:url" content="{{ route('product.show', $product->slug) }}">
<meta property="og:site_name" content="{{ config('app.name') }}">
@if($product->images->first())
<meta property="og:image" content="{{ asset('storage/' . $product->images->first()->path) }}">
@endif
<meta property="product:price:amount" content="{{ $product->variants->min('price') ?? 0 }}">
<meta property="product:price:currency" content="TRY">
@endsection

@section('twitter_card')
<meta name="twitter:card" content="product">
<meta name="twitter:title" content="{{ $product->meta_title ?? $product->title }}">
<meta name="twitter:description" content="{{ $product->meta_description ?? strip_tags($product->description) }}">
@if($product->images->first())
<meta name="twitter:image" content="{{ asset('storage/' . $product->images->first()->path) }}">
@endif
@endsection

@section('schema')
@php
    $productSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->title,
        'description' => strip_tags($product->description),
        'sku' => $product->variants->first()?->sku ?? '',
        'brand' => [
            '@type' => 'Brand',
            'name' => config('app.name')
        ],
        'offers' => [
            '@type' => 'AggregateOffer',
            'lowPrice' => $product->variants->min('price') ?? 0,
            'highPrice' => $product->variants->max('price') ?? 0,
            'priceCurrency' => 'TRY',
            'availability' => ($product->variants->first()?->current_stock ?? 0) > 0 
                ? 'https://schema.org/InStock' 
                : 'https://schema.org/OutOfStock'
        ]
    ];
    if ($product->images->first()) {
        $productSchema['image'] = asset('storage/' . $product->images->first()->path);
    }
@endphp
<script type="application/ld+json">
{{ json_encode($productSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}
</script>
@endsection

@php
$selectedVariant = $product->variants->first();
$minPrice = $product->variants->count() > 0 ? $product->variants->min('price') : null;
$maxPrice = $product->variants->count() > 0 ? $product->variants->max('price') : null;
$inStock = $selectedVariant && $selectedVariant->current_stock > 0;
@endphp

<style>
    .product-page {
        padding-top: 85px;
    }
    
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: var(--color-muted);
        margin-bottom: 32px;
    }
    
    .breadcrumb a {
        color: var(--color-muted);
        text-decoration: none;
        transition: color 0.2s;
    }
    
    .breadcrumb a:hover {
        color: var(--color-secondary);
    }
    
    .breadcrumb span {
        color: var(--color-secondary);
    }
    
    .product-container {
        display: grid;
        grid-template-columns: 55% 45%;
        gap: 60px;
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 40px 80px;
    }
    
    @media (max-width: 1024px) {
        .product-container {
            grid-template-columns: 1fr;
            gap: 40px;
            padding: 0 24px 60px;
        }
    }
    
    /* Gallery Styles */
    .product-gallery {
        display: flex;
        gap: 16px;
    }
    
    .thumbnail-strip {
        display: flex;
        flex-direction: column;
        gap: 12px;
        width: 80px;
        flex-shrink: 0;
        overflow-y: auto;
        max-height: 500px;
    }
    
    .thumbnail-item {
        width: 80px;
        height: 80px;
        border-radius: 4px;
        overflow: hidden;
        cursor: pointer;
        opacity: 0.5;
        transition: opacity 0.2s, border-color 0.2s;
        border: 2px solid transparent;
    }
    
    .thumbnail-item:hover,
    .thumbnail-item.active {
        opacity: 1;
        border-color: var(--color-secondary);
    }
    
    .thumbnail-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .main-image-container {
        flex: 1;
        position: relative;
        aspect-ratio: 1;
        overflow: hidden;
        border-radius: 8px;
        background: #f9f9f9;
    }
    
    .main-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
        cursor: zoom-in;
    }
    
    .main-image-container:hover .main-image {
        transform: scale(1.05);
    }
    
    @media (max-width: 768px) {
        .product-gallery {
            flex-direction: column-reverse;
        }
        
        .thumbnail-strip {
            flex-direction: row;
            width: 100%;
            max-height: none;
            overflow-x: auto;
        }
        
        .thumbnail-item {
            width: 60px;
            height: 60px;
            flex-shrink: 0;
        }
    }
    
    /* Product Info Styles */
    .product-info {
        padding-top: 20px;
    }
    
    .product-category {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.2em;
        color: var(--color-muted);
        margin-bottom: 12px;
    }
    
    .product-title {
        font-size: 36px;
        font-weight: 400;
        color: var(--color-secondary);
        margin-bottom: 24px;
        line-height: 1.2;
    }
    
    .product-price {
        font-size: 28px;
        font-weight: 600;
        color: var(--color-secondary);
        margin-bottom: 32px;
    }
    
    .product-price .original-price {
        font-size: 18px;
        color: var(--color-muted);
        text-decoration: line-through;
        margin-left: 12px;
    }
    
    /* Variant Selector */
    .variant-section {
        margin-bottom: 32px;
    }
    
    .variant-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        color: var(--color-muted);
        margin-bottom: 12px;
    }
    
    .variant-options {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .variant-option {
        padding: 10px 20px;
        border: 1px solid var(--color-border);
        border-radius: 4px;
        background: white;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.2s;
    }
    
    .variant-option:hover {
        border-color: var(--color-secondary);
    }
    
    .variant-option.selected {
        background: var(--color-secondary);
        color: white;
        border-color: var(--color-secondary);
    }
    
    /* Quantity Selector */
    .quantity-section {
        margin-bottom: 32px;
    }
    
    .quantity-selector {
        display: inline-flex;
        align-items: center;
        border: 1px solid var(--color-border);
        border-radius: 4px;
        overflow: hidden;
    }
    
    .quantity-btn {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border: none;
        cursor: pointer;
        font-size: 18px;
        transition: background 0.2s;
    }
    
    .quantity-btn:hover {
        background: #f5f5f5;
    }
    
    .quantity-input {
        width: 60px;
        height: 44px;
        text-align: center;
        border: none;
        border-left: 1px solid var(--color-border);
        border-right: 1px solid var(--color-border);
        font-size: 16px;
        font-weight: 500;
    }
    
    .quantity-input:focus {
        outline: none;
    }
    
    /* Add to Cart Button */
    .add-to-cart-section {
        margin-bottom: 40px;
    }
    
    .add-to-cart-btn {
        width: 100%;
        padding: 18px 32px;
        background: var(--color-secondary);
        color: white;
        border: none;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.2em;
        cursor: pointer;
        transition: background 0.2s;
        border-radius: 4px;
    }
    
    .add-to-cart-btn:hover {
        background: var(--color-hover);
    }
    
    .add-to-cart-btn:disabled {
        background: var(--color-muted);
        cursor: not-allowed;
    }
    
    .stock-status {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        margin-bottom: 24px;
    }
    
    .stock-status.in-stock {
        color: #16a34a;
    }
    
    .stock-status.out-of-stock {
        color: #dc2626;
    }
    
    .stock-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    
    .stock-status.in-stock .stock-dot {
        background: #16a34a;
    }
    
    .stock-status.out-of-stock .stock-dot {
        background: #dc2626;
    }
    
    /* Product Tabs */
    .product-tabs {
        border-top: 1px solid var(--color-border);
        margin-top: 40px;
    }
    
    .tab-buttons {
        display: flex;
        border-bottom: 1px solid var(--color-border);
    }
    
    .tab-btn {
        padding: 20px 24px;
        background: none;
        border: none;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        color: var(--color-muted);
        cursor: pointer;
        position: relative;
        transition: color 0.2s;
    }
    
    .tab-btn:hover {
        color: var(--color-secondary);
    }
    
    .tab-btn.active {
        color: var(--color-secondary);
    }
    
    .tab-btn.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--color-secondary);
    }
    
    .tab-content {
        padding: 32px 0;
        display: none;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .tab-content p {
        font-size: 14px;
        line-height: 1.8;
        color: var(--color-text);
    }
    
    .specs-table {
        width: 100%;
    }
    
    .specs-table tr {
        border-bottom: 1px solid var(--color-border);
    }
    
    .specs-table td {
        padding: 16px 0;
        font-size: 14px;
    }
    
    .specs-table td:first-child {
        font-weight: 500;
        color: var(--color-muted);
        width: 40%;
    }
    
    /* Trust Badges */
    .trust-badges {
        display: flex;
        gap: 24px;
        padding: 24px 0;
        border-top: 1px solid var(--color-border);
        margin-top: 32px;
    }
    
    .trust-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: var(--color-muted);
    }
    
    .trust-badge svg {
        width: 20px;
        height: 20px;
    }
    
    /* Related Products */
    .related-section {
        padding: 80px 40px;
        background: #fafafa;
    }
    
    .related-title {
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.2em;
        color: var(--color-muted);
        text-align: center;
        margin-bottom: 48px;
    }
    
    .related-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 32px;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    @media (max-width: 1024px) {
        .related-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .related-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        
        .related-section {
            padding: 60px 24px;
        }
    }
</style>

@section('content')
<div class="product-page">
    <!-- Breadcrumb -->
    <div class="product-container">
        <nav class="breadcrumb">
            <a href="{{ route('home') }}">Ana Sayfa</a>
            <span>/</span>
            <a href="{{ route('category.show', $product->category->slug ?? '#') }}">{{ $product->category->name ?? '' }}</a>
            <span>/</span>
            <span>{{ $product->title }}</span>
        </nav>
    </div>

    <!-- Product Section -->
    <div class="product-container">
        <!-- Gallery -->
        <div class="product-gallery">
            <div class="thumbnail-strip" id="thumbnailStrip">
                @foreach($product->images as $index => $image)
                <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}" data-image="{{ asset('storage/' . $image->path) }}">
                    <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $product->title }}" loading="lazy">
                </div>
                @endforeach
            </div>
            <div class="main-image-container">
                @if($product->images->first())
                <img id="mainImage" src="{{ asset('storage/' . $product->images->first()->path) }}" alt="{{ $product->title }}" class="main-image" loading="lazy">
                @else
                <div class="main-image-container" style="display:flex;align-items:center;justify-content:center;">
                    <span style="color: var(--color-muted);">Resim Yok</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Product Info -->
        <div class="product-info">
            <p class="product-category">{{ $product->category->name ?? '' }}</p>
            <h1 class="product-title">{{ $product->title }}</h1>
            
            <div class="product-price">
                @if($minPrice && $minPrice == $maxPrice)
                    ₺{{ number_format($minPrice, 2) }}
                @elseif($minPrice && $maxPrice)
                    ₺{{ number_format($minPrice, 2) }} - ₺{{ number_format($maxPrice, 2) }}
                @else
                    Fiyat bilgisi için iletişime geçin
                @endif
            </div>

            <!-- Stock Status -->
            <div class="stock-status {{ $inStock ? 'in-stock' : 'out-of-stock' }}">
                <span class="stock-dot"></span>
                @if($inStock)
                    Stokta ({{ $selectedVariant->current_stock }} adet)
                @else
                    Stokta Yok
                @endif
            </div>

            <!-- Variant Selection -->
            @if($product->variants->count() > 0)
            <div class="variant-section">
                <p class="variant-label">Seçenekler</p>
                <div class="variant-options" id="variantOptions">
                    @foreach($product->variants as $variant)
                    <button type="button" 
                            class="variant-option {{ $variant->id === $selectedVariant->id ? 'selected' : '' }}"
                            data-id="{{ $variant->id }}"
                            data-price="{{ $variant->price }}"
                            data-stock="{{ $variant->current_stock }}"
                            data-sku="{{ $variant->sku }}"
                            onclick="selectVariant(this)">
                        @if($variant->attributes && is_array($variant->attributes))
                            {{ implode(', ', $variant->attributes) }}
                        @elseif($variant->attributes && is_string($variant->attributes))
                            {{ $variant->attributes }}
                        @else
                            {{ $variant->sku }}
                        @endif
                        - ₺{{ number_format($variant->price, 0) }}
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Quantity -->
            <div class="quantity-section">
                <p class="variant-label">Adet</p>
                <div class="quantity-selector">
                    <button type="button" class="quantity-btn" onclick="changeQuantity(-1)">−</button>
                    <input type="number" id="quantity" value="1" min="1" max="{{ $selectedVariant->current_stock ?? 1 }}" class="quantity-input">
                    <button type="button" class="quantity-btn" onclick="changeQuantity(1)">+</button>
                </div>
            </div>

            <!-- Add to Cart -->
            <div class="add-to-cart-section">
                <button type="button" class="add-to-cart-btn" id="addToCartBtn" onclick="addToCartFromDetail()">
                    Sepete Ekle
                </button>
            </div>
            @else
            <div class="add-to-cart-section">
                <button type="button" class="add-to-cart-btn" disabled>
                    Ürün Mevcut Değil
                </button>
            </div>
            @endif

            <!-- Trust Badges -->
            <div class="trust-badges">
                <div class="trust-badge">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    Ücretsiz Kargo
                </div>
                <div class="trust-badge">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    30 Gün İade
                </div>
                <div class="trust-badge">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Güvenli Ödeme
                </div>
            </div>

            <!-- Product Tabs -->
            <div class="product-tabs">
                <div class="tab-buttons">
                    <button type="button" class="tab-btn active" onclick="openTab('description')">Açıklama</button>
                    <button type="button" class="tab-btn" onclick="openTab('specs')">Özellikler</button>
                    <button type="button" class="tab-btn" onclick="openTab('shipping')">Kargo & İade</button>
                </div>
                
                <div id="description" class="tab-content active">
                    <p>{!! nl2br(e($product->description)) !!}</p>
                </div>
                
                <div id="specs" class="tab-content">
                    <table class="specs-table">
                        <tr>
                            <td>SKU</td>
                            <td>{{ $selectedVariant->sku ?? '-' }}</td>
                        </tr>
                        @if($selectedVariant && $selectedVariant->attributes && is_array($selectedVariant->attributes))
                            @foreach($selectedVariant->attributes as $key => $value)
                            <tr>
                                <td>{{ ucfirst($key) }}</td>
                                <td>{{ $value }}</td>
                            </tr>
                            @endforeach
                        @endif
                    </table>
                </div>
                
                <div id="shipping" class="tab-content">
                    <p><strong>Teslimat:</strong> Siparişiniz 3-5 iş günü içinde kargoya verilir. Ücretsiz kargo seçeneği ile kapınıza kadar teslim edilir.</p>
                    <br>
                    <p><strong>İade:</strong> Ürünü 30 gün içinde herhangi bir nedenle iade edebilirsiniz. İade koşulları için iletişim sayfamızı ziyaret edin.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <section class="related-section">
        <h3 class="related-title">Benzer Ürünler</h3>
        <div class="related-grid">
            @foreach($relatedProducts as $related)
                @php
                    $relVariant = $related->variants->first();
                    $relImage = $related->images->first();
                @endphp
                <a href="{{ route('product.show', $related->slug) }}" class="product-card" style="text-decoration: none;">
                    <div class="product-image">
                        @if($relImage)
                            <img src="{{ asset('storage/' . $relImage->path) }}" alt="{{ $related->title }}" loading="lazy">
                        @else
                            <img src="https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400&q=80" alt="{{ $related->title }}" loading="lazy">
                        @endif
                    </div>
                    <h4 class="product-name">{{ $related->title }}</h4>
                    <p class="product-price">₺{{ number_format($related->variants->count() > 0 ? $related->variants->min('price') : 0, 2) }}</p>
                </a>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection

@section('scripts')
<script>
    let currentVariant = null;

    function initVariant() {
        const firstVariant = document.querySelector('.variant-option');
        if (firstVariant) {
            selectVariant(firstVariant);
        }
    }

    function selectVariant(element) {
        // Update selected state
        document.querySelectorAll('.variant-option').forEach(opt => opt.classList.remove('selected'));
        element.classList.add('selected');
        
        // Update data
        currentVariant = {
            id: element.dataset.id,
            price: parseFloat(element.dataset.price),
            stock: parseInt(element.dataset.stock),
            sku: element.dataset.sku
        };
        
        // Update price display
        document.querySelector('.product-price').innerHTML = '₺' + currentVariant.price.toLocaleString('tr-TR', {minimumFractionDigits: 2});
        
        // Update stock status
        const stockStatus = document.querySelector('.stock-status');
        if (currentVariant.stock > 0) {
            stockStatus.className = 'stock-status in-stock';
            stockStatus.innerHTML = '<span class="stock-dot"></span>Stokta (' + currentVariant.stock + ' adet)';
            document.getElementById('addToCartBtn').disabled = false;
            document.getElementById('addToCartBtn').textContent = 'Sepete Ekle';
            document.getElementById('quantity').max = currentVariant.stock;
        } else {
            stockStatus.className = 'stock-status out-of-stock';
            stockStatus.innerHTML = '<span class="stock-dot"></span>Stokta Yok';
            document.getElementById('addToCartBtn').disabled = true;
            document.getElementById('addToCartBtn').textContent = 'Stokta Yok';
        }
    }

    function changeImage(url, element) {
        document.getElementById('mainImage').src = url;
        document.querySelectorAll('.thumbnail-item').forEach(item => item.classList.remove('active'));
        element.classList.add('active');
    }

    function changeQuantity(delta) {
        const input = document.getElementById('quantity');
        let value = parseInt(input.value) + delta;
        const max = parseInt(input.max) || 99;
        
        if (value < 1) value = 1;
        if (value > max) value = max;
        
        input.value = value;
    }

    function openTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        
        document.getElementById(tabName).classList.add('active');
        event.target.classList.add('active');
    }

    function addToCartFromDetail() {
        if (!currentVariant) {
            initVariant();
        }
        
        if (!currentVariant || currentVariant.stock <= 0) {
            showToast('Bu ürün stokta mevcut değil', 'error');
            return;
        }
        
        const quantity = parseInt(document.getElementById('quantity').value);
        
        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                variant_id: currentVariant.id,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('cart-count').textContent = data.cart_count;
                document.getElementById('addToCartBtn').textContent = 'Sepete Eklendi!';
                showToast('Ürün sepete eklendi', 'success');
                setTimeout(() => {
                    document.getElementById('addToCartBtn').textContent = 'Sepete Ekle';
                }, 2000);
            } else {
                showToast(data.error || 'Sepete ekleme başarısız', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Bir hata oluştu', 'error');
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initVariant();
        
        // Thumbnail click handlers
        document.querySelectorAll('.thumbnail-item').forEach(item => {
            item.addEventListener('click', function() {
                changeImage(this.dataset.image, this);
            });
        });
    });
</script>
@endsection
