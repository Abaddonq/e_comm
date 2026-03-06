@extends('layouts.web')

@section('title', ' - Register')

@include('auth.partials.styles')

@section('content')
<x-auth.card :title="__('Register')" :subtitle="__('Register subtitle')">

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <x-auth.input
                    name="name"
                    type="text"
                    :label="__('Full Name')"
                    :value="old('name')"
                    autocomplete="name"
                    required
                    autofocus
                />

                <x-auth.input
                    name="email"
                    type="email"
                    :label="__('Email')"
                    :value="old('email')"
                    autocomplete="username"
                    required
                />

                <x-auth.input
                    name="password"
                    type="password"
                    :label="__('Password')"
                    autocomplete="new-password"
                    required
                />

                <x-auth.input
                    name="password_confirmation"
                    type="password"
                    :label="__('Password Confirm')"
                    autocomplete="new-password"
                    required
                />

                <x-auth.button>{{ __('Sign Up') }}</x-auth.button>
            </form>

            <p class="auth-footer">
                {{ __('Already have account?') }}
                <a href="{{ route('login') }}" class="text-link">{{ __('Login') }}</a>
            </p>
</x-auth.card>
@endsection
