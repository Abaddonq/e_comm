@props([
    'type' => 'submit',
])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'auth-btn']) }}>
    {{ $slot }}
</button>
