{{-- Step 2 of sign-up and of password reset. --}}
<x-layouts.auth :title="$title">
    <x-auth-steps :steps="$steps" :current="2" />

    <h1 class="h3 font-display mb-1">Check your email</h1>
    <p class="intake-muted mb-4">
        We sent a 6-digit code to <strong class="text-body-emphasis">{{ $maskedEmail }}</strong>.
        It works for {{ \App\Support\EmailOtp::EXPIRES_MINUTES }} minutes.
    </p>

    @if (session('notice'))
        <div class="alert alert-success small" role="status">{{ session('notice') }}</div>
    @endif

    <form method="POST" action="{{ $action }}" class="vstack gap-3" novalidate>
        @csrf
        <div>
            <label class="form-label" for="code">6-digit code</label>
            <input id="code" name="code" @class(['form-control form-control-lg text-center font-monospace tw:tracking-[0.5em]', 'is-invalid' => $errors->has('code')]) inputmode="numeric" pattern="\d{6}" maxlength="6" autocomplete="one-time-code" required autofocus>
            <x-field-error for="code">The code is 6 digits. Check your email again.</x-field-error>
            <div class="form-text">Can't find it? Look in your spam folder.</div>
        </div>
        <button type="submit" class="btn btn-success w-100" data-busy="Checking…"><i class="fa-solid fa-circle-check me-1" aria-hidden="true"></i> Continue</button>
    </form>

    <div class="d-flex justify-content-between align-items-center gap-2 small mt-3">
        <a href="{{ $backHref }}">Use a different email</a>
        <form method="POST" action="{{ $resendAction }}">
            @csrf
            <button type="submit" class="btn btn-link btn-sm p-0" data-resend-in="{{ $resendIn }}"><i class="fa-solid fa-rotate-right me-1" aria-hidden="true"></i> <span data-resend-label>Resend code</span></button>
        </form>
    </div>
</x-layouts.auth>
