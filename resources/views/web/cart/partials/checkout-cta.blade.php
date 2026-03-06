@php
    $buttonClass = $class ?? 'theme-btn';
@endphp

@auth
    <a href="{{ route('checkout.index') }}" class="{{ $buttonClass }}">{{ __('Proceed to Checkout') }}</a>
@else
    <a href="{{ route('login') . '?redirect=' . urlencode(route('checkout.index')) }}" class="{{ $buttonClass }}">{{ __('Login to Checkout') }}</a>
@endauth
