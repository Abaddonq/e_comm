@extends('layouts.web')

@section('title', ' - ' . $category->name)

@section('meta_description', $category->meta_description ?? 'Shop ' . $category->name . ' at DecorMotto. Browse our collection of ' . $category->name . ' products.')

@section('canonical_url')
<link rel="canonical" href="{{ route('category.show', $category->slug) }}">
@endsection

@section('open_graph')
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $category->meta_title ?? $category->name }} - {{ config('app.name') }}">
<meta property="og:description" content="{{ $category->meta_description ?? 'Shop ' . $category->name . ' products' }}">
<meta property="og:url" content="{{ route('category.show', $category->slug) }}">
<meta property="og:site_name" content="{{ config('app.name') }}">
@endsection

@section('twitter_card')
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $category->meta_title ?? $category->name }}">
<meta name="twitter:description" content="{{ $category->meta_description ?? 'Shop ' . $category->name . ' products' }}">
@endsection

@section('content')
<div class="bg-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex mb-4 text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-gray-700">Home</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">{{ $category->name }}</span>
        </nav>

        <h1 class="text-3xl font-bold text-gray-900 mb-8">{{ $category->name }}</h1>

        <div class="flex justify-between items-center mb-6">
            <p class="text-gray-600">{{ $products->total() }} products</p>
            <select onchange="window.location = this.value" class="border rounded px-3 py-1">
                <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}">Newest</option>
                <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
            </select>
        </div>

        @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
            @php
                $firstVariant = $product->variants->first();
                $minPrice = $product->variants->min('price');
                $firstImage = $product->images->first();
            @endphp
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                <a href="{{ route('product.show', $product->slug) }}">
                    @if($firstImage)
                    <img src="{{ asset('storage/' . $firstImage->path) }}" alt="{{ $product->title }}" class="w-full h-48 object-cover">
                    @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400">No Image</span>
                    </div>
                    @endif
                </a>
                <div class="p-4">
                    <a href="{{ route('product.show', $product->slug) }}">
                        <h3 class="text-lg font-semibold text-gray-900 hover:text-indigo-600">{{ $product->title }}</h3>
                    </a>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xl font-bold text-gray-900">₺{{ number_format($minPrice ?? 0, 2) }}</span>
                        @if($firstVariant && $firstVariant->current_stock > 0)
                        <button onclick="addToCart({{ $firstVariant->id }})" class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 text-sm">Add to Cart</button>
                        @else
                        <span class="text-gray-400 text-sm">Out of Stock</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-8">
            {{ $products->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg">No products found in this category.</p>
        </div>
        @endif
    </div>
</div>
@endsection
