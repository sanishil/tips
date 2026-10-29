<x-layouts.auth title="Reset your password">
    <div data-steps>
        <ol class="list-unstyled d-flex gap-2 mb-4" aria-label="Steps">
            @foreach (['Email', 'Code', 'New password'] as $index => $label)
                <li class="flex-fill vstack gap-1">
                    <span class="step-bar" data-step-indicator="{{ $index + 1 }}" data-active="{{ $index === 0 ? 'true' : 'false' }}"></span>
                    <span class="small intake-muted">{{ $label }}</span>
                </li>
            @endforeach
        </ol>

        {{-- Step 1: ask for a code --}}
        <div data-step="1">
            <h1 class="h3 font-display mb-1">Reset your password</h1>
            <p class="intake-muted">We'll email you a 6-digit code.</p>
            <div class="vstack gap-3">
                <div>
                    <label class="form-label" for="reset_email">Email</label>
                    <input id="reset_email" type="email" class="form-control" autocomplete="email" required>
                    <x-field-error for="email">Enter the email address on your account.</x-field-error>
                </div>
                <button type="button" class="btn btn-primary w-100" data-step-go="2">Send code</button>
            </div>
        </div>

        {{-- Step 2: check the code --}}
        <div data-step="2" hidden>
            <h1 class="h3 font-display mb-1">Check your email</h1>
            <p class="intake-muted">We sent a code to <strong class="text-body-emphasis" data-masked-email></strong>. It works for 10 minutes.</p>
            <div class="vstack gap-3">
                <div>
                    <label class="form-label" for="otp">6-digit code</label>
                    <input id="otp" class="form-control form-control-lg text-center font-monospace tw:tracking-[0.5em]" inputmode="numeric" pattern="\d{6}" maxlength="6" autocomplete="one-time-code" required>
                    <x-field-error for="otp">The code is 6 digits — check your email again.</x-field-error>
                    <div class="form-text">You can try 5 times before you need a new code.</div>
                </div>
                <button type="button" class="btn btn-primary w-100" data-step-go="3">Continue</button>
                <div class="d-flex justify-content-between gap-2 small">
                    <button type="button" class="btn btn-link btn-sm p-0" data-step-go="1">Use a different email</button>
                    <button type="button" class="btn btn-link btn-sm p-0" data-resend disabled>Resend code</button>
                </div>
            </div>
        </div>

        {{-- Step 3: new password --}}
        <form method="POST" data-step="3" hidden data-demo-form data-redirect="{{ route('login', ['reset' => 1]) }}" novalidate>
            @csrf
            <h1 class="h3 font-display mb-1">Choose a new password</h1>
            <p class="intake-muted">You'll be signed out on any other device.</p>
            <div class="vstack gap-3">
                <div>
                    <label class="form-label" for="new_password">New password</label>
                    <input id="new_password" name="password" type="password" class="form-control" autocomplete="new-password" minlength="8" required>
                    <x-field-error for="password">Use at least 8 characters.</x-field-error>
                </div>
                <div>
                    <label class="form-label" for="new_password_confirmation">Type it again</label>
                    <input id="new_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" data-match="new_password" required>
                    <x-field-error for="password_confirmation">The two passwords don't match.</x-field-error>
                </div>
                <button type="submit" class="btn btn-primary w-100" data-busy="Saving…">Save new password</button>
            </div>
        </form>
    </div>

    <p class="small text-center mt-4 mb-0">
        <a href="{{ route('login') }}">Back to sign in</a>
    </p>
</x-layouts.auth>
