@extends('layouts.web')

@section('title', ' - New Address')

@php
    $redirectTo = old('redirect_to', request('redirect_to'));
    $cancelRoute = $redirectTo === 'checkout' ? route('checkout.index') : route('addresses.index');
@endphp

<style>
    .address-create-page {
        min-height: 100vh;
        padding-top: 85px;
        background: #fafafa;
    }

    .address-create-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 24px 80px;
    }

    .address-create-card {
        max-width: 860px;
        margin: 0 auto;
        background: #fff;
        border: 1px solid var(--color-border);
        border-radius: 14px;
        padding: 26px;
    }

    .address-create-title {
        font-size: 32px;
        font-weight: 400;
        letter-spacing: 0.03em;
        color: var(--color-secondary);
        margin-bottom: 8px;
    }

    .address-create-subtitle {
        font-size: 14px;
        color: var(--color-muted);
        margin-bottom: 22px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .field {
        margin-bottom: 2px;
    }

    .field-full {
        grid-column: span 2;
    }

    .field label {
        display: block;
        margin-bottom: 6px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--color-muted);
    }

    .field input,
    .field select {
        width: 100%;
        height: 46px;
        border: 1px solid var(--color-border);
        border-radius: 8px;
        padding: 0 12px;
        background: #fff;
        font-size: 14px;
        color: var(--color-secondary);
    }

    .field input:focus,
    .field select:focus {
        outline: none;
        border-color: var(--color-secondary);
    }

    .error-text {
        margin-top: 6px;
        font-size: 12px;
        color: #b91c1c;
    }

    .checkbox-row {
        grid-column: span 2;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 4px;
    }

    .checkbox-row input {
        accent-color: #1a1a1a;
    }

    .checkbox-row label {
        font-size: 13px;
        color: var(--color-secondary);
    }

    .actions {
        margin-top: 20px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn {
        min-height: 44px;
        padding: 10px 18px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        transition: background var(--transition-fast), border-color var(--transition-fast), color var(--transition-fast);
    }

    .btn-primary {
        border: 1px solid var(--color-secondary);
        background: var(--color-secondary);
        color: #fff;
    }

    .btn-primary:hover {
        background: var(--color-hover);
        border-color: var(--color-hover);
    }

    .btn-secondary {
        border: 1px solid var(--color-border);
        background: #fff;
        color: var(--color-secondary);
    }

    .btn-secondary:hover {
        border-color: var(--color-secondary);
    }

    @media (max-width: 640px) {
        .address-create-page {
            padding-top: 70px;
        }

        .address-create-container {
            padding: 28px 16px 52px;
        }

        .address-create-card {
            padding: 20px 16px;
        }

        .address-create-title {
            font-size: 26px;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .field-full,
        .checkbox-row {
            grid-column: span 1;
        }

        .actions .btn {
            width: 100%;
            text-align: center;
        }
    }
</style>

@section('content')
<div class="address-create-page">
    <div class="address-create-container">
        <div class="address-create-card">
            <h1 class="address-create-title">{{ __('New Address') }}</h1>
            <p class="address-create-subtitle">{{ __('New address subtitle') }}</p>

            <form action="{{ route('addresses.store') }}" method="POST">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">

                <div class="form-grid">
                    <div class="field">
                        <label for="full_name">{{ __('Full Name') }}</label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                        @error('full_name')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="phone">{{ __('Phone') }}</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required>
                        @error('phone')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field field-full">
                        <label for="address_line1">{{ __('Address Line 1') }}</label>
                        <input type="text" id="address_line1" name="address_line1" value="{{ old('address_line1') }}" required>
                        @error('address_line1')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field field-full">
                        <label for="address_line2">{{ __('Address Line 2 (Optional)') }}</label>
                        <input type="text" id="address_line2" name="address_line2" value="{{ old('address_line2') }}">
                    </div>

                    <div class="field">
                        <label for="city">{{ __('City') }}</label>
                        <input type="text" id="city" name="city" value="{{ old('city') }}" required>
                        @error('city')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="state">{{ __('District') }}</label>
                        <input type="text" id="state" name="state" value="{{ old('state') }}">
                    </div>

                    <div class="field">
                        <label for="postal_code">{{ __('Postal Code') }}</label>
                        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required>
                        @error('postal_code')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="country">{{ __('Country') }}</label>
                        <select id="country" name="country" required>
                            <option value="TR" {{ old('country') == 'TR' ? 'selected' : '' }}>Turkey</option>
                            <option value="US" {{ old('country') == 'US' ? 'selected' : '' }}>United States</option>
                            <option value="GB" {{ old('country') == 'GB' ? 'selected' : '' }}>United Kingdom</option>
                            <option value="DE" {{ old('country') == 'DE' ? 'selected' : '' }}>Germany</option>
                            <option value="FR" {{ old('country') == 'FR' ? 'selected' : '' }}>France</option>
                            <option value="IT" {{ old('country') == 'IT' ? 'selected' : '' }}>Italy</option>
                            <option value="ES" {{ old('country') == 'ES' ? 'selected' : '' }}>Spain</option>
                            <option value="NL" {{ old('country') == 'NL' ? 'selected' : '' }}>Netherlands</option>
                        </select>
                        @error('country')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="checkbox-row">
                        <input type="checkbox" id="is_default" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                        <label for="is_default">{{ __('Set as default address') }}</label>
                    </div>
                </div>

                <div class="actions">
                    <a href="{{ $cancelRoute }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Save Address') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
