@extends('layouts.web')

@section('title', ' - Forgot Password')

@section('content')
<x-auth.card :title="__('Password Reset')" :subtitle="__('Password reset subtitle')">

            @if (session('status'))
                <div class="status-box">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
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

                <x-auth.button>{{ __('Send Reset Link') }}</x-auth.button>
            </form>

            <p class="auth-footer">
                <a href="{{ route('login') }}" class="text-link">{{ __('Back to Login') }}</a>
            </p>
</x-auth.card>
@endsection
