@extends('layouts.web')

@section('title', ' - ' . $category->name)

@section('meta_description', $category->meta_description ?? 'Shop ' . $category->name . ' at DecorMotto.')

@section('canonical_url')
<link rel="canonical" href="{{ route('category.show', $category->slug) }}">
@endsection

<style>
    .category-page {
        padding-top: 85px;
        min-height: 100vh;
        background: #fafafa;
    }

    .category-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 24px 80px;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        color: #999;
        margin-bottom: 24px;
    }

    .breadcrumb a {
        color: #666;
        text-decoration: none;
    }

    .breadcrumb a:hover {
        color: #1a1a1a;
    }

    .breadcrumb span {
        color: #1a1a1a;
    }

    .category-header {
        margin-bottom: 32px;
    }

    .category-title {
        font-size: 40px;
        font-weight: 500;
        color: #1a1a1a;
    }

    .category-count {
        font-size: 15px;
        color: #666;
        margin-top: 8px;
    }

    .category-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        background: white;
        border-radius: 12px;
        margin-bottom: 32px;
    }

    .toolbar-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .filter-toggle-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        color: #333;
        cursor: pointer;
    }

    .filter-toggle-btn:hover {
        border-color: #1a1a1a;
    }

    .filter-toggle-btn svg {
        width: 18px;
        height: 18px;
    }

    .results-count {
        font-size: 14px;
        color: #666;
    }

    .sort-dropdown {
        position: relative;
    }

    .sort-select {
        appearance: none;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px 40px 10px 16px;
        font-size: 14px;
        color: #333;
        cursor: pointer;
    }

    .sort-select:focus {
        outline: none;
        border-color: #1a1a1a;
    }

    .filter-panel {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        display: none;
    }

    .filter-panel.active {
        display: block;
    }

    .filter-row {
        display: flex;
        align-items: center;
        gap: 24px;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .filter-label {
        font-size: 14px;
        font-weight: 500;
        color: #333;
    }

    .price-inputs {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .price-input {
        width: 100px;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
    }

    .price-input:focus {
        outline: none;
        border-color: #1a1a1a;
    }

    .filter-checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-checkbox {
        width: 18px;
        height: 18px;
    }

    .filter-actions {
        display: flex;
        gap: 12px;
        margin-left: auto;
    }

    .filter-btn {
        padding: 8px 20px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        border: none;
    }

    .filter-btn-primary {
        background: #1a1a1a;
        color: white;
    }

    .filter-btn-secondary {
        background: white;
        border: 1px solid #ddd;
        color: #333;
    }

    .active-filters {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .active-filter {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 20px;
        font-size: 13px;
    }

    .active-filter-remove {
        cursor: pointer;
        color: #666;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    @media (min-width: 768px) {
        .products-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .products-grid {
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }
    }

    .product-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.1);
    }

    .product-image {
        position: relative;
        aspect-ratio: 1;
        background: #f5f5f5;
        overflow: hidden;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.05);
    }

    .product-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        background: #1a1a1a;
        color: white;
    }

    .wishlist-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .product-card:hover .wishlist-btn {
        opacity: 1;
    }

    .wishlist-btn:hover {
        background: #f5f5f5;
    }

    .quick-add-btn {
        position: absolute;
        bottom: 12px;
        left: 12px;
        right: 12px;
        background: #1a1a1a;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 12px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.3s ease;
    }

    .product-card:hover .quick-add-btn {
        opacity: 1;
        transform: translateY(0);
    }

    .quick-add-btn:hover {
        background: #333;
    }

    .product-info {
        padding: 16px;
    }

    .product-name {
        font-size: 14px;
        font-weight: 500;
        color: #1a1a1a;
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-name a {
        color: inherit;
        text-decoration: none;
    }

    .product-price {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
    }

    .product-stock {
        font-size: 12px;
        color: #dc2626;
        margin-top: 4px;
    }

    .pagination-wrap {
        margin-top: 48px;
        display: flex;
        justify-content: center;
    }

    .pagination {
        display: flex;
        gap: 8px;
    }

    .pagination a, .pagination span {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0 12px;
        border-radius: 8px;
        font-size: 14px;
        color: #444;
        text-decoration: none;
    }

    .pagination a {
        background: white;
        border: 1px solid #eee;
    }

    .pagination a:hover {
        border-color: #1a1a1a;
    }

    .pagination span.current {
        background: #1a1a1a;
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
    }

    .empty-state svg {
        width: 80px;
        height: 80px;
        color: #ccc;
        margin-bottom: 20px;
    }

    .empty-state h2 {
        font-size: 22px;
        color: #333;
    }
</style>

<div class="category-page">
    <div class="category-container">
        <nav class="breadcrumb">
            <a href="{{ route('home') }}">Anasayfa</a>
            <span>/</span>
            <span>{{ $category->name }}</span>
        </nav>

        <div class="category-header">
            <h1 class="category-title">{{ $category->name }}</h1>
            <p class="category-count">{{ $products->total() }} ürün</p>
        </div>

        <div class="category-toolbar">
            <div class="toolbar-left">
                <button class="filter-toggle-btn" onclick="toggleFilters()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtrele
                </button>
                <span class="results-count">{{ $products->count() }} ürün gösteriliyor</span>
            </div>

            <div class="sort-dropdown">
                <select class="sort-select" onchange="window.location = this.value">
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>En Yeni</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Fiyat: Düşükten Yükseğe</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Fiyat: Yüksekten Düşüğe</option>
                </select>
            </div>
        </div>

        <div class="filter-panel" id="filterPanel">
            <form method="GET" action="{{ route('category.show', $category->slug) }}">
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                <div class="filter-row">
                    <div class="filter-group">
                        <span class="filter-label">Fiyat:</span>
                        <div class="price-inputs">
                            <input type="number" name="min_price" class="price-input" placeholder="Min" value="{{ request('min_price') }}">
                            <span>-</span>
                            <input type="number" name="max_price" class="price-input" placeholder="Max" value="{{ request('max_price') }}">
                        </div>
                    </div>
                    <div class="filter-checkbox-wrapper">
                        <input type="checkbox" name="in_stock" id="in_stock" class="filter-checkbox" value="1" {{ request('in_stock') ? 'checked' : '' }}>
                        <label for="in_stock">Sadece Stokta Olanlar</label>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn filter-btn-primary">Uygula</button>
                        <a href="{{ route('category.show', $category->slug) }}" class="filter-btn filter-btn-secondary">Temizle</a>
                    </div>
                </div>
            </form>
        </div>

        @if(request('min_price') || request('max_price') || request('in_stock'))
            <div class="active-filters">
                @if(request('min_price'))
                    <span class="active-filter">
                        Min: ₺{{ request('min_price') }}
                        <span class="active-filter-remove" onclick="removeFilter('min_price')">×</span>
                    </span>
                @endif
                @if(request('max_price'))
                    <span class="active-filter">
                        Max: ₺{{ request('max_price') }}
                        <span class="active-filter-remove" onclick="removeFilter('max_price')">×</span>
                    </span>
                @endif
                @if(request('in_stock'))
                    <span class="active-filter">
                        Stokta Olanlar
                        <span class="active-filter-remove" onclick="removeFilter('in_stock')">×</span>
                    </span>
                @endif
            </div>
        @endif

        @if($products->count() > 0)
            <div class="products-grid">
                @foreach($products as $product)
                    @php
                        $minPrice = $product->variants->count() > 0 ? $product->variants->min('price') : 0;
                        $firstVariant = $product->variants->first();
                        $image = $product->images->first();
                        $inStock = $firstVariant && $firstVariant->current_stock > 0;
                    @endphp
                    <div class="product-card">
                        <a href="{{ route('product.show', $product->slug) }}">
                            <div class="product-image">
                                @if($image)
                                    <img src="{{ asset('storage/' . $image->path) }}" alt="{{ e($product->title) }}" loading="lazy">
                                @else
                                    <div style="display:flex;align-items:center;justify-content:center;height:100%;color:#999;">
                                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                @if($product->is_new)
                                    <span class="product-badge">Yeni</span>
                                @endif
                                <button class="wishlist-btn" onclick="toggleWishlist({{ $product->id }}, event)">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                </button>
                                <button class="quick-add-btn" onclick="quickAdd({{ $product->id }}, event)">Sepete Ekle</button>
                            </div>
                        </a>
                        <div class="product-info">
                            <h3 class="product-name">
                                <a href="{{ route('product.show', $product->slug) }}">{{ e($product->title) }}</a>
                            </h3>
                            <div class="product-price">₺{{ number_format($minPrice, 2) }}</div>
                            @if(!$inStock)
                                <div class="product-stock">Stokta Yok</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($products->hasPages())
                <div class="pagination-wrap">
                    {{ $products->appends(request()->except('page'))->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <h2>Ürün Bulunamadı</h2>
            </div>
        @endif
    </div>
</div>

<script>
    function toggleFilters() {
        document.getElementById('filterPanel').classList.toggle('active');
    }

    function removeFilter(param) {
        const url = new URL(window.location.href);
        url.searchParams.delete(param);
        window.location.href = url.toString();
    }

    function quickAdd(productId, event) {
        event.preventDefault();
        event.stopPropagation();

        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('cart-count').textContent = data.cart_count;
                showToast('Ürün sepete eklendi', 'success');
            } else {
                showToast(data.error || 'Sepete ekleme başarısız', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Bir hata oluştu', 'error');
        });
    }
</script>
