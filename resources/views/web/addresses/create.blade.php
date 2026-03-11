@extends('layouts.web')

@section('title', ' - New Address')

@php
    $redirectTo = old('redirect_to', request('redirect_to'));
    $cancelRoute = $redirectTo === 'checkout' ? route('checkout.index') : route('profile.index', ['tab' => 'addresses']);
@endphp

@section('content')
<div class="address-page">
    <div class="address-container">
        <div class="address-card">
            <h1 class="address-title">{{ __('New Address') }}</h1>
            <p class="address-subtitle">{{ __('New address subtitle') }}</p>

            <form action="{{ route('addresses.store') }}" method="POST">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">

                @include('web.addresses.partials.form-fields')

                <div class="actions">
                    <a href="{{ $cancelRoute }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Save Address') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
