<div class="wishlist-grid" id="wishlistGrid">
    @if($wishlistProducts->count() > 0)
        @foreach($wishlistProducts as $wishlist)
            @php
                $product = $wishlist->product;
            @endphp
            @if($product)
                @php
                    $minPrice = $product->variants->count() > 0 ? $product->variants->min('price') : 0;
                    $image = $product->images->first();
                @endphp
                <div class="wishlist-item">
                    <a href="{{ route('product.show', $product->slug) }}" class="wishlist-image">
                        @if($image)
                            <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $product->title }}">
                        @else
                            <div class="no-image">{{ __('No Image') }}</div>
                        @endif
                    </a>
                    <div class="wishlist-info">
                        <h3><a href="{{ route('product.show', $product->slug) }}">{{ $product->title }}</a></h3>
                        <div class="wishlist-price">₺{{ number_format($minPrice, 2) }}</div>
                        <div class="wishlist-actions">
                            <button class="btn btn-sm btn-primary" onclick="quickAdd({{ $product->id }}, event)">{{ __('Add to Cart') }}</button>
                            <button class="btn btn-sm btn-secondary" onclick="removeFromWishlist({{ $product->id }})">{{ __('Remove') }}</button>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @else
        <div class="empty-wishlist">
            <svg width="64" height="64" fill="none" stroke="#ccc" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            <h3>{{ __('No Wishlist Yet') }}</h3>
            <p>{{ __('Add products you like to your wishlist') }}</p>
            <a href="{{ route('home') }}" class="btn btn-primary">{{ __('Start Shopping') }}</a>
        </div>
    @endif
</div>
