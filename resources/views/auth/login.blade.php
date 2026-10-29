@php
    /* Each state can be previewed with ?error=mismatch|captcha|locked|active, ?timeout=1, ?signed_out=1 or ?reset=1 */
    $errorMessages = [
        'mismatch' => 'That email and password don\'t match. Check both and try again.',
        'captcha' => 'The answer to the sum was wrong. Here\'s a new one.',
        'locked' => 'Too many wrong attempts from this device. Please wait 12 minutes before trying again.',
        'active' => 'This account is already signed in somewhere else. Sign out there first, or wait 10 minutes.',
    ];
    $errorMessage = $errorMessages[request('error')] ?? null;

    $notice = match (true) {
        (bool) request('timeout') => 'You were signed out after 10 minutes without activity.',
        (bool) request('signed_out') => 'You\'ve signed out.',
        (bool) request('reset') => 'Your password has been changed. Sign in with the new one.',
        default => null,
    };
@endphp

<x-layouts.auth title="Sign in">
    <p class="form-part-title mb-2"><i class="fa-solid fa-plus" aria-hidden="true"></i> Portal</p>
    <h1 class="h3 font-display mb-1">Sign in</h1>
    <p class="intake-muted mb-4">For admins and student coordinators.</p>

    @if ($errorMessage)
        <div class="alert alert-danger small" role="alert">{{ $errorMessage }}</div>
    @elseif ($notice)
        <div class="alert alert-success small" role="status">{{ $notice }}</div>
    @endif

    <form method="POST" class="vstack gap-3" data-demo-form data-redirect="{{ route('admin.dashboard') }}" novalidate>
        @csrf
        <div>
            <label class="form-label" for="email">Email</label>
            <input id="email" name="email" type="email" class="form-control" autocomplete="username" required autofocus value="{{ old('email') }}">
            <x-field-error for="email">Enter the email address your account was created with.</x-field-error>
        </div>

        <div>
            <label class="form-label" for="password">Password</label>
            <div class="input-group has-validation">
                <input id="password" name="password" type="password" class="form-control" autocomplete="current-password" required>
                <button type="button" class="btn btn-outline-secondary" data-password-toggle="password" aria-label="Show password" aria-pressed="false">
                    <i class="fa-regular fa-eye fa-fw" aria-hidden="true"></i>
                </button>
                <x-field-error for="password">Enter your password.</x-field-error>
            </div>
        </div>

        <div>
            <label class="form-label" for="captcha">What is <span id="captcha-question" class="font-monospace fw-semibold">7 + 4 = ?</span></label>
            <div class="input-group has-validation">
                <input id="captcha" name="captcha" class="form-control" inputmode="numeric" pattern="-?\d+" autocomplete="off" required>
                <button type="button" class="btn btn-outline-secondary" data-captcha-refresh="captcha-question" aria-label="Give me a different sum" title="Different sum">
                    <i class="fa-solid fa-rotate" aria-hidden="true"></i>
                </button>
                <x-field-error for="captcha">Answer the sum so we know you're not a bot.</x-field-error>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 mt-2" data-busy="Signing in…">Sign in</button>
    </form>

    <p class="small text-center mt-4 mb-0">
        <a href="{{ route('password.request') }}">Forgot your password?</a>
    </p>
</x-layouts.auth>
