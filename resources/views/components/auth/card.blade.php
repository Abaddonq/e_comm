@props([
    'title',
    'subtitle' => null,
    'maxWidth' => '560px',
])

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card" style="max-width: {{ $maxWidth }};">
            <h1 class="auth-title">{{ $title }}</h1>
            @if($subtitle)
                <p class="auth-subtitle">{{ $subtitle }}</p>
            @endif

            {{ $slot }}
        </div>
    </div>
</div>
