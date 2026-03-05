@extends('layouts.web')

@section('title', ' - Login')

<style>
    .auth-page {
        min-height: 100vh;
        padding-top: 85px;
        background: #fafafa;
    }

    .auth-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 24px 80px;
    }

    .auth-card {
        max-width: 520px;
        margin: 0 auto;
        background: #fff;
        border: 1px solid var(--color-border);
        border-radius: 14px;
        padding: 28px;
    }

    .auth-title {
        font-size: 32px;
        font-weight: 400;
        letter-spacing: 0.03em;
        color: var(--color-secondary);
        margin-bottom: 6px;
    }

    .auth-subtitle {
        color: var(--color-muted);
        font-size: 14px;
        margin-bottom: 22px;
    }

    .status-box {
        margin-bottom: 14px;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13px;
    }

    .field {
        margin-bottom: 14px;
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

    .field input {
        width: 100%;
        height: 46px;
        border: 1px solid var(--color-border);
        border-radius: 8px;
        padding: 0 12px;
        background: #fff;
        font-size: 14px;
        color: var(--color-secondary);
    }

    .field input:focus {
        outline: none;
        border-color: var(--color-secondary);
    }

    .error-text {
        margin-top: 6px;
        color: #b91c1c;
        font-size: 12px;
    }

    .auth-row {
        margin-top: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .remember-wrap {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--color-secondary);
    }

    .remember-wrap input {
        accent-color: #1a1a1a;
    }

    .text-link {
        color: var(--color-secondary);
        text-decoration: none;
        font-size: 12px;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        font-weight: 600;
    }

    .text-link:hover {
        color: var(--color-hover);
    }

    .auth-btn {
        width: 100%;
        margin-top: 18px;
        height: 48px;
        border: 1px solid var(--color-secondary);
        border-radius: 8px;
        background: var(--color-secondary);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        cursor: pointer;
        transition: background var(--transition-fast), border-color var(--transition-fast);
    }

    .auth-btn:hover {
        background: var(--color-hover);
        border-color: var(--color-hover);
    }

    .auth-footer {
        margin-top: 18px;
        text-align: center;
        font-size: 13px;
        color: var(--color-muted);
    }

    @media (max-width: 640px) {
        .auth-page { padding-top: 70px; }
        .auth-container { padding: 28px 16px 52px; }
        .auth-card { padding: 22px 16px; }
        .auth-title { font-size: 26px; }
        .auth-row {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

@section('content')
<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <h1 class="auth-title">{{ __('Login') }}</h1>
            <p class="auth-subtitle">{{ __('Login subtitle') }}</p>

            @if (session('status'))
                <div class="status-box">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field">
                    <label for="email">{{ __('Email') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    @error('email')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password">
                    @error('password')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="auth-row">
                    <label for="remember_me" class="remember-wrap">
                        <input id="remember_me" type="checkbox" name="remember">
                        <span>{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-link" href="{{ route('password.request') }}">{{ __('Forgot Password') }}</a>
                    @endif
                </div>

                <button type="submit" class="auth-btn">{{ __('Login') }}</button>
            </form>

            <p class="auth-footer">
                {{ __('No account?') }}
                <a href="{{ route('register') }}" class="text-link">{{ __('Register') }}</a>
            </p>
        </div>
    </div>
</div>
@endsection
