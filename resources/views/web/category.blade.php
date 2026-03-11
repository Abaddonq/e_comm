@extends('layouts.web')

@section('title', ' - ' . $category->name)

@section('meta_description', $category->meta_description ?? 'Shop ' . $category->name . ' at DecorMotto.')

@section('canonical_url')
<link rel="canonical" href="{{ route('category.show', $category->slug) }}">
@endsection


<div class="category-page">
    <div class="category-container">
        <nav class="breadcrumb">
            <a href="{{ route('home') }}">{{ __('Homepage') }}</a>
            <span>/</span>
            <span>{{ $category->name }}</span>
        </nav>

        <div class="category-header">
            <h1 class="category-title">{{ $category->name }}</h1>
            <p class="category-count">{{ $products->total() }} {{ __('products_count') }}</p>
        </div>

        <div class="category-toolbar">
            <div class="toolbar-left">
                <button type="button" class="filter-toggle-btn" onclick="toggleFilters()" aria-label="{{ __('Filter') }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span class="filter-toggle-text">{{ __('Filter') }}</span>
                </button>
                <span class="results-count">{{ $products->count() }} {{ __('showing products') }}</span>
            </div>

            <div class="sort-dropdown">
                <select class="sort-select" onchange="window.location = this.value">
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>{{ __('Newest') }}</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
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
                        <span class="filter-label">{{ __('Price:') }}</span>
                        <div class="price-inputs">
                            <input type="number" name="min_price" class="price-input" placeholder="Min" value="{{ request('min_price') }}">
                            <span>-</span>
                            <input type="number" name="max_price" class="price-input" placeholder="Max" value="{{ request('max_price') }}">
                        </div>
                    </div>
                    <div class="filter-checkbox-wrapper">
                        <input type="checkbox" name="in_stock" id="in_stock" class="filter-checkbox" value="1" {{ request('in_stock') ? 'checked' : '' }}>
                        <label for="in_stock">{{ __('In Stock Only') }}</label>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn filter-btn-primary">{{ __('Apply') }}</button>
                        <a href="{{ route('category.show', $category->slug) }}" class="filter-btn filter-btn-secondary">{{ __('Clear') }}</a>
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
                        {{ __('In Stock Items') }}
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
                                    <div class="category-image-placeholder">
                                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                @if($product->is_new)
                                    <span class="product-badge">{{ __('New') }}</span>
                                @endif
                                <button class="wishlist-btn{{ in_array($product->id, $wishlistProductIds ?? []) ? ' active' : '' }}" id="wishlist-btn-{{ $product->id }}" onclick="toggleWishlist({{ $product->id }}, event)">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                </button>
                                <button type="button" class="quick-add-btn" onclick="quickAdd({{ $product->id }}, event)" aria-label="{{ __('Add to Cart') }}" title="{{ __('Add to Cart') }}">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </button>
                            </div>
                        </a>
                        <div class="product-info">
                            <h3 class="product-name">
                                <a href="{{ route('product.show', $product->slug) }}">{{ e($product->title) }}</a>
                            </h3>
                            <div class="product-price">₺{{ number_format($minPrice, 2) }}</div>
                            @if(!$inStock)
                                <div class="product-stock">{{ __('Out of Stock') }}</div>
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
                <h2>{{ __('No Products Found') }}</h2>
            </div>
        @endif
    </div>
</div>

