@if($orders->count() > 0)
    <div class="orders-list">
        @foreach($orders as $order)
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div class="order-number">{{ __('Order') }} {{ $order->order_number }}</div>
                        <div class="order-date">{{ $order->created_at->format('d.m.Y') }}</div>
                    </div>
                    <span class="order-status {{ $order->effective_fulfillment_status }}">
                        {{ $order->customer_status_label }}
                    </span>
                </div>
                <div class="order-products-preview">
                    @foreach($order->items->take(4) as $item)
                        @php
                            $product = $item->variant->product ?? null;
                            $image = $product && $product->images->first() ? $product->images->first() : null;
                        @endphp
                        @if($image)
                            <div class="order-product-thumb">
                                <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $product->title }}">
                            </div>
                        @else
                            <div class="order-product-thumb placeholder">
                                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                    @endforeach
                    @if($order->items->count() > 4)
                        <div class="order-product-more">+{{ $order->items->count() - 4 }}</div>
                    @endif
                </div>
                <div class="order-summary">
                    <span class="order-items-count">{{ $order->items->count() }} {{ __('products_count') }}</span>
                    <span class="order-total">₺{{ number_format($order->total, 2) }}</span>
                </div>
                <div class="order-actions">
                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-secondary btn-sm">{{ __('Order Details') }}</a>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="empty-state">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        <h3>{{ __('No Orders Yet') }}</h3>
        <p>{{ __('Start shopping to place your first order') }}</p>
        <a href="{{ route('home') }}" class="btn btn-primary">{{ __('Start Shopping') }}</a>
    </div>
@endif
