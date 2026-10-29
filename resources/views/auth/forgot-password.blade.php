<x-layouts.auth title="Reset your password">
    <x-auth-steps :steps="$steps" :current="1" />

    <h1 class="h3 font-display mb-1">Reset your password</h1>
    <p class="intake-muted mb-4">Enter the email you sign in with and we'll send you a 6-digit code.</p>

    <form method="POST" action="{{ route('password.request') }}" class="vstack gap-3" novalidate>
        @csrf
        <div>
            <label class="form-label" for="email">Email</label>
            <input id="email" name="email" type="email" @class(['form-control', 'is-invalid' => $errors->has('email')]) autocomplete="email" required autofocus value="{{ old('email') }}">
            <x-field-error for="email">Enter your email address.</x-field-error>
        </div>

        <x-recaptcha />

        <button type="submit" class="btn btn-success w-100 mt-2" data-busy="Sending code…"><i class="fa-solid fa-paper-plane me-1" aria-hidden="true"></i> Send code</button>
    </form>

    <p class="small text-center mt-4 mb-0">
        Remembered it? <a href="{{ route('login') }}">Back to sign in</a>
    </p>
</x-layouts.auth>
