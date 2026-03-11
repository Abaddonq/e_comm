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

@section('content')
<div class="product-page">
    <!-- Breadcrumb -->
    <div class="product-container">
        <nav class="breadcrumb">
            <a href="{{ route('home') }}">{{ __('Home') }}</a>
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
                <div class="main-image-fallback">
                    <span class="main-image-fallback-text">{{ __('No Image') }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Product Info -->
        <div class="product-info">
            <p class="product-category">{{ $product->category->name ?? '' }}</p>
            <div class="product-title-row">
                <h1 class="product-title">{{ $product->title }}</h1>
                <button class="wishlist-btn-detail" id="wishlistBtnDetail" onclick="toggleWishlistDetail({{ $product->id }})">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span id="wishlistText">{{ __('Add to Wishlist') }}</span>
                </button>
            </div>
            
            <div class="product-price">
                @if($minPrice && $minPrice == $maxPrice)
                    ₺{{ number_format($minPrice, 2) }}
                @elseif($minPrice && $maxPrice)
                    ₺{{ number_format($minPrice, 2) }} - ₺{{ number_format($maxPrice, 2) }}
                @else
                    {{ __('Contact for price') }}
                @endif
            </div>

            <!-- Stock Status -->
            <div class="stock-status {{ $inStock ? 'in-stock' : 'out-of-stock' }}">
                <span class="stock-dot"></span>
                @if($inStock)
                    {{ __('In Stock') }} ({{ $selectedVariant->current_stock }} {{ __('pieces') }})
                @else
                    {{ __('Out of Stock') }}
                @endif
            </div>

            <!-- Variant Selection -->
            @if($product->variants->count() > 0)
            <div class="variant-section">
                <p class="variant-label">{{ __('Options') }}</p>
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
                <p class="variant-label">{{ __('Quantity') }}</p>
                <div class="quantity-selector">
                    <button type="button" class="quantity-btn" onclick="changeQuantity(-1)">−</button>
                    <input type="number" id="quantity" value="1" min="1" max="{{ $selectedVariant->current_stock ?? 1 }}" class="quantity-input">
                    <button type="button" class="quantity-btn" onclick="changeQuantity(1)">+</button>
                </div>
            </div>

            <!-- Add to Cart -->
            <div class="add-to-cart-section">
                <button type="button" class="add-to-cart-btn" id="addToCartBtn" onclick="addToCartFromDetail()">
                    {{ __('Add to Cart') }}
                </button>
            </div>
            @else
            <div class="add-to-cart-section">
                <button type="button" class="add-to-cart-btn" disabled>
                    {{ __('Product Not Available') }}
                </button>
            </div>
            @endif

            <!-- Trust Badges -->
            <div class="trust-badges">
                <div class="trust-badge">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    {{ __('Free Shipping') }}
                </div>
                <div class="trust-badge">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    {{ __('30 Day Returns') }}
                </div>
                <div class="trust-badge">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    {{ __('Secure Payment') }}
                </div>
            </div>

            <!-- Product Tabs -->
            <div class="product-tabs">
                <div class="tab-buttons">
                    <button type="button" class="tab-btn active" onclick="openTab('description')">{{ __('Description') }}</button>
                    <button type="button" class="tab-btn" onclick="openTab('specs')">{{ __('Specifications') }}</button>
                    <button type="button" class="tab-btn" onclick="openTab('shipping')">{{ __('Shipping & Returns') }}</button>
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
                    <p><strong>{{ __('Delivery:') }}</strong> {{ __('Shipping info') }}</p>
                    <br>
                    <p><strong>{{ __('Return:') }}</strong> {{ __('Return info') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <section class="related-section">
        <h3 class="related-title">{{ __('Related Products') }}</h3>
        <div class="related-grid">
            @foreach($relatedProducts as $related)
                @php
                    $relVariant = $related->variants->first();
                    $relImage = $related->images->first();
                @endphp
                <a href="{{ route('product.show', $related->slug) }}" class="product-card related-product-link">
                    <div class="product-image">
                        @if($relImage)
                            <img src="{{ asset('storage/' . $relImage->path) }}" alt="{{ $related->title }}" loading="lazy">
                        @else
                            <img src="/img/mock-img.jpg" alt="{{ $related->title }}" loading="lazy">
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

    // Check if product is in wishlist on page load
    document.addEventListener('DOMContentLoaded', function() {
        checkWishlistStatus();
    });

    function checkWishlistStatus() {
        const productId = {{ $product->id }};
        
        fetch('{{ route("wishlist.check") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.is_wishlisted) {
                const btn = document.getElementById('wishlistBtnDetail');
                btn.classList.add('active');
                document.getElementById('wishlistText').textContent = window.__t['In Wishlist'];
            }
        })
        .catch(error => {
            console.error('Error checking wishlist:', error);
        });
    }

    function toggleWishlistDetail(productId) {
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
                const btn = document.getElementById('wishlistBtnDetail');
                const text = document.getElementById('wishlistText');
                
                if (data.is_added) {
                    btn.classList.add('active');
                    text.textContent = window.__t['In Wishlist'];
                    showToast(window.__t['Product added to wishlist'], 'success');
                } else {
                    btn.classList.remove('active');
                    text.textContent = window.__t['Add to Wishlist'];
                    showToast(window.__t['Product removed from wishlist'], 'success');
                }
            } else if (data.error) {
                showToast(data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast(window.__t['An error occurred'], 'error');
        });
    }

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
            stockStatus.innerHTML = '<span class="stock-dot"></span>' + window.__t['In Stock'] + ' (' + currentVariant.stock + ' ' + window.__t['pieces'] + ')';
            document.getElementById('addToCartBtn').disabled = false;
            document.getElementById('addToCartBtn').textContent = window.__t['Add to Cart'];
            document.getElementById('quantity').max = currentVariant.stock;
        } else {
            stockStatus.className = 'stock-status out-of-stock';
            stockStatus.innerHTML = '<span class="stock-dot"></span>' + window.__t['Out of Stock'];
            document.getElementById('addToCartBtn').disabled = true;
            document.getElementById('addToCartBtn').textContent = window.__t['Out of Stock'];
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
            showToast(window.__t['Product not in stock'], 'error');
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
                document.getElementById('addToCartBtn').textContent = window.__t['Added to Cart!'];
                showToast(window.__t['Product added to cart'], 'success');
                setTimeout(() => {
                    document.getElementById('addToCartBtn').textContent = window.__t['Add to Cart'];
                }, 2000);
            } else {
                showToast(data.error || window.__t['Add to cart failed'], 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast(window.__t['An error occurred'], 'error');
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
