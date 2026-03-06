@php
    $editing = isset($address) && $address;
@endphp

<div class="form-grid">
    <div class="field">
        <label for="full_name">{{ __('Full Name') }}</label>
        <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $editing ? $address->full_name : '') }}" required>
        @error('full_name')
            <p class="error-text">{{ $message }}</p>
        @enderror
    </div>

    <div class="field">
        <label for="phone">{{ __('Phone') }}</label>
        <input type="text" id="phone" name="phone" value="{{ old('phone', $editing ? $address->phone : '') }}" required>
        @error('phone')
            <p class="error-text">{{ $message }}</p>
        @enderror
    </div>

    <div class="field field-full">
        <label for="address_line1">{{ __('Address Line 1') }}</label>
        <input type="text" id="address_line1" name="address_line1" value="{{ old('address_line1', $editing ? $address->address_line1 : '') }}" required>
        @error('address_line1')
            <p class="error-text">{{ $message }}</p>
        @enderror
    </div>

    <div class="field field-full">
        <label for="address_line2">{{ __('Address Line 2 (Optional)') }}</label>
        <input type="text" id="address_line2" name="address_line2" value="{{ old('address_line2', $editing ? $address->address_line2 : '') }}">
    </div>

    <div class="field">
        <label for="city">{{ __('City') }}</label>
        <input type="text" id="city" name="city" value="{{ old('city', $editing ? $address->city : '') }}" required>
        @error('city')
            <p class="error-text">{{ $message }}</p>
        @enderror
    </div>

    <div class="field">
        <label for="state">{{ __('District') }}</label>
        <input type="text" id="state" name="state" value="{{ old('state', $editing ? $address->state : '') }}">
    </div>

    <div class="field">
        <label for="postal_code">{{ __('Postal Code') }}</label>
        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $editing ? $address->postal_code : '') }}" required>
        @error('postal_code')
            <p class="error-text">{{ $message }}</p>
        @enderror
    </div>

    <div class="field">
        <label for="country">{{ __('Country') }}</label>
        <select id="country" name="country" required>
            @php $selectedCountry = old('country', $editing ? $address->country : 'TR'); @endphp
            <option value="TR" {{ $selectedCountry == 'TR' ? 'selected' : '' }}>Turkey</option>
            <option value="US" {{ $selectedCountry == 'US' ? 'selected' : '' }}>United States</option>
            <option value="GB" {{ $selectedCountry == 'GB' ? 'selected' : '' }}>United Kingdom</option>
            <option value="DE" {{ $selectedCountry == 'DE' ? 'selected' : '' }}>Germany</option>
            <option value="FR" {{ $selectedCountry == 'FR' ? 'selected' : '' }}>France</option>
            <option value="IT" {{ $selectedCountry == 'IT' ? 'selected' : '' }}>Italy</option>
            <option value="ES" {{ $selectedCountry == 'ES' ? 'selected' : '' }}>Spain</option>
            <option value="NL" {{ $selectedCountry == 'NL' ? 'selected' : '' }}>Netherlands</option>
        </select>
        @error('country')
            <p class="error-text">{{ $message }}</p>
        @enderror
    </div>

    <div class="checkbox-row">
        <input type="checkbox" id="is_default" name="is_default" value="1" {{ old('is_default', $editing ? $address->is_default : false) ? 'checked' : '' }}>
        <label for="is_default">{{ __('Set as default address') }}</label>
    </div>
</div>
