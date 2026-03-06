@props([
    'name',
    'label',
    'type' => 'text',
    'id' => null,
    'value' => null,
    'autocomplete' => null,
    'required' => false,
    'autofocus' => false,
])

@php
    $fieldId = $id ?? $name;
@endphp

<div class="field">
    <label for="{{ $fieldId }}">{{ $label }}</label>
    <input
        id="{{ $fieldId }}"
        type="{{ $type }}"
        name="{{ $name }}"
        @if($type !== 'password' && !is_null($value)) value="{{ $value }}" @endif
        @if($required) required @endif
        @if($autofocus) autofocus @endif
        @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        {{ $attributes }}
    >
    @error($name)
        <p class="error-text">{{ $message }}</p>
    @enderror
</div>
