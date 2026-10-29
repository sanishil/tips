@php
    $notice = session('notice') ?? (request('timeout') ? 'You were signed out after 10 minutes without activity.' : null);
@endphp

<x-layouts.auth title="Sign in">
    <h1 class="h3 font-display mb-4">Sign in</h1>

    @if ($notice)
        <div class="alert alert-success small" role="status">{{ $notice }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="vstack gap-3" novalidate>
        @csrf
        <div>
            <label class="form-label" for="email">Email</label>
            <input id="email" name="email" type="email" @class(['form-control', 'is-invalid' => $errors->has('email')]) autocomplete="username" required autofocus value="{{ old('email') }}">
            <x-field-error for="email">Enter your email address.</x-field-error>
        </div>

        <div>
            <label class="form-label" for="password">Password</label>
            <div class="input-group has-validation">
                <input id="password" name="password" type="password" @class(['form-control', 'is-invalid' => $errors->has('password')]) autocomplete="current-password" required @if (old('email')) autofocus @endif>
                <button type="button" class="btn btn-outline-secondary" data-password-toggle="password" aria-label="Show password" aria-pressed="false">
                    <i class="fa-regular fa-eye fa-fw" aria-hidden="true"></i>
                </button>
                <x-field-error for="password">Enter your password.</x-field-error>
            </div>
        </div>

        <x-recaptcha />

        <button type="submit" class="btn btn-success w-100 mt-2" data-busy="Signing in…"><i class="fa-solid fa-right-to-bracket me-1" aria-hidden="true"></i> Sign in</button>
    </form>

    <div class="d-flex justify-content-between gap-3 small mt-4">
        <a href="{{ route('password.request') }}">Forgot your password?</a>
        <a href="{{ route('register') }}">Create an account</a>
    </div>
</x-layouts.auth>
