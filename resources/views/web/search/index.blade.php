@extends('layouts.web')

@section('title', ' - Ara: ' . $query)

<style>
    .search-page {
        padding-top: 85px;
        min-height: 100vh;
        background: #fafafa;
    }

    .search-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 24px 80px;
    }

    .search-header {
        margin-bottom: 32px;
    }

    .search-header h1 {
        font-size: 28px;
        font-weight: 600;
        color: #1a1a1a;
    }

    .search-header p {
        color: #666;
        font-size: 14px;
        margin-top: 8px;
    }

    .search-query {
        color: #1a1a1a;
        font-weight: 600;
    }

    .search-results-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
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
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .product-image {
        position: relative;
        aspect-ratio: 1;
        overflow: hidden;
        background: #f5f5f5;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.05);
    }

    .product-info {
        padding: 16px;
    }

    .product-name {
        font-size: 15px;
        font-weight: 500;
        color: #1a1a1a;
        margin-bottom: 8px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-name a {
        color: inherit;
        text-decoration: none;
    }

    .product-name a:hover {
        color: #666;
    }

    .product-price {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
    }

    .no-results {
        text-align: center;
        padding: 80px 20px;
    }

    .no-results svg {
        width: 80px;
        height: 80px;
        color: #ccc;
        margin-bottom: 20px;
    }

    .no-results h2 {
        font-size: 22px;
        color: #333;
        margin-bottom: 12px;
    }

    .no-results p {
        color: #666;
        font-size: 15px;
    }

    .highlight {
        background: #fef3c7;
        padding: 0 2px;
        border-radius: 2px;
    }

    .pagination-wrap {
        margin-top: 40px;
        display: flex;
        justify-content: center;
    }

    .pagination {
        display: flex;
        gap: 8px;
    }

    .pagination a,
    .pagination span {
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
        transition: all 0.2s;
    }

    .pagination a {
        background: white;
        border: 1px solid #eee;
    }

    .pagination a:hover {
        border-color: #1a1a1a;
        color: #1a1a1a;
    }

    .pagination span.current {
        background: #1a1a1a;
        color: white;
        border: 1px solid #1a1a1a;
    }
</style>

<div class="search-page">
    <div class="search-container">
        <div class="search-header">
            <h1>Arama Sonuçları</h1>
            <p>"<span class="search-query">{{ e($query) }}</span>" için {{ $products->total() }} sonuç bulundu</p>
        </div>

        @if($products->count() > 0)
            <div class="search-results-grid">
                @foreach($products as $product)
                    @php
                        $minPrice = $product->variants->count() > 0 ? $product->variants->min('price') : 0;
                        $image = $product->images->first();
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
                            </div>
                        </a>
                        <div class="product-info">
                            <h3 class="product-name">
                                <a href="{{ route('product.show', $product->slug) }}">{{ e($product->title) }}</a>
                            </h3>
                            <div class="product-price">₺{{ number_format($minPrice, 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($products->hasPages())
                <div class="pagination-wrap">
                    {{ $products->appends(['q' => $query])->links() }}
                </div>
            @endif
        @else
            <div class="no-results">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <h2>Sonuç Bulunamadı</h2>
                <p>"{{ e($query }}" için ürün bulunamadı. Farklı anahtar kelimeler deneyebilirsiniz.</p>
            </div>
        @endif
    </div>
</div>
