@php
    /* Each state can be previewed with ?error=mismatch|captcha|locked|active, ?timeout=1, ?signed_out=1 or ?reset=1 */
    $errorMessages = [
        'mismatch' => 'That email and password don\'t match. Check both and try again.',
        'captcha' => 'The "I\'m not a robot" check failed or expired. Please tick it again.',
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
    <h1 class="h3 font-display mb-1">Sign in</h1>
    <p class="intake-muted mb-4">Enter your details to continue.</p>

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

        <div data-recaptcha>
            <div class="recaptcha-row">
                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" data-callback="recaptchaChecked"></div>
                <button type="button" class="btn btn-icon fs-5" data-recaptcha-reset aria-label="Load a new “I'm not a robot” check" title="New check">
                    <i class="fa-solid fa-rotate" aria-hidden="true"></i>
                </button>
            </div>
            <x-field-error for="g-recaptcha-response">Tick "I'm not a robot" so we know you're not a bot.</x-field-error>
        </div>

        <button type="submit" class="btn btn-success w-100 mt-2" data-busy="Signing in…"><i class="fa-solid fa-right-to-bracket"></i> Sign in</button>
    </form>

    <p class="small text-center mt-4 mb-0">
        <a href="{{ route('password.request') }}">Forgot your password?</a>
    </p>

    <link rel="preconnect" href="https://www.google.com">
    <link rel="preconnect" href="https://www.gstatic.com" crossorigin>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</x-layouts.auth>
