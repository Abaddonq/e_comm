@extends('layouts.web')

@section('title', ' - Reset Password')

@include('auth.partials.styles')

@section('content')
<x-auth.card :title="__('Set New Password')" :subtitle="__('Set new password subtitle')">

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <x-auth.input
                    name="email"
                    type="email"
                    :label="__('Email')"
                    :value="old('email', $request->email)"
                    autocomplete="username"
                    required
                    autofocus
                />

                <x-auth.input
                    name="password"
                    type="password"
                    :label="__('New Password')"
                    autocomplete="new-password"
                    required
                />

                <x-auth.input
                    name="password_confirmation"
                    type="password"
                    :label="__('New Password Confirm')"
                    autocomplete="new-password"
                    required
                />

                <x-auth.button>{{ __('Update Password') }}</x-auth.button>
            </form>

            <p class="auth-footer">
                <a href="{{ route('login') }}" class="text-link">{{ __('Back to Login') }}</a>
            </p>
</x-auth.card>
@endsection
