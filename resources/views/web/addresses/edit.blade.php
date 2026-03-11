@extends('layouts.web')

@section('title', ' - ' . __('Edit Address'))

@section('content')
<div class="address-page">
    <div class="address-container">
        <div class="address-card">
            <h1 class="address-title">{{ __('Edit Address') }}</h1>
            <p class="address-subtitle">{{ __('New address subtitle') }}</p>

            <form action="{{ route('addresses.update', $address->id) }}" method="POST">
                @csrf
                @method('PUT')

                @include('web.addresses.partials.form-fields', ['address' => $address])

                <div class="actions">
                    <a href="{{ route('profile.index', ['tab' => 'addresses']) }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Save Address') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
