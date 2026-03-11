@extends('layouts.web')

@section('title', ' - Login')

@section('content')
<x-auth.card :title="__('Login')" :subtitle="__('Login subtitle')" max-width="520px">

            @if (session('status'))
                <div class="status-box">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <x-auth.input
                    name="email"
                    type="email"
                    :label="__('Email')"
                    :value="old('email')"
                    autocomplete="username"
                    required
                    autofocus
                />

                <x-auth.input
                    name="password"
                    type="password"
                    :label="__('Password')"
                    autocomplete="current-password"
                    required
                />

                <div class="auth-row">
                    <label for="remember_me" class="remember-wrap">
                        <input id="remember_me" type="checkbox" name="remember">
                        <span>{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-link" href="{{ route('password.request') }}">{{ __('Forgot Password') }}</a>
                    @endif
                </div>

                <x-auth.button class="auth-btn-spaced">{{ __('Login') }}</x-auth.button>
            </form>

            <p class="auth-footer">
                {{ __('No account?') }}
                <a href="{{ route('register') }}" class="text-link">{{ __('Register') }}</a>
            </p>
</x-auth.card>
@endsection
