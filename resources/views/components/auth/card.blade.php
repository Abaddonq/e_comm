@props([
    'title',
    'subtitle' => null,
    'maxWidth' => '560px',
])

@php
    $maxWidthClass = match ($maxWidth) {
        '520px' => 'auth-card-compact',
        default => 'auth-card-default',
    };
@endphp

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card {{ $maxWidthClass }}">
            <h1 class="auth-title">{{ $title }}</h1>
            @if($subtitle)
                <p class="auth-subtitle">{{ $subtitle }}</p>
            @endif

            {{ $slot }}
        </div>
    </div>
</div>
