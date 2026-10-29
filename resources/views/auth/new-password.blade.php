{{-- Last step of password reset, and the forced change after signing in with an emailed password. --}}
<x-layouts.auth :title="$title">
    @if ($steps)
        <x-auth-steps :steps="$steps" :current="3" />
    @endif

    <h1 class="h3 font-display mb-1">{{ $heading }}</h1>
    <p class="intake-muted mb-4">{{ $intro }}</p>

    <form method="POST" action="{{ $action }}" class="vstack gap-3" novalidate>
        @csrf
        <div>
            <label class="form-label" for="password">New password</label>
            <div class="input-group has-validation">
                <input id="password" name="password" type="password" @class(['form-control', 'is-invalid' => $errors->has('password')]) autocomplete="new-password" minlength="8" required autofocus>
                <button type="button" class="btn btn-outline-secondary" data-password-toggle="password" aria-label="Show password" aria-pressed="false">
                    <i class="fa-regular fa-eye fa-fw" aria-hidden="true"></i>
                </button>
                <x-field-error for="password">Use at least 8 characters.</x-field-error>
            </div>
        </div>
        <div>
            <label class="form-label" for="password_confirmation">Type it again</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" data-match="password" required>
            <x-field-error for="password_confirmation">The two passwords don't match.</x-field-error>
        </div>
        <button type="submit" class="btn btn-success w-100 mt-2" data-busy="Saving…"><i class="fa-solid fa-key me-1" aria-hidden="true"></i> Save password</button>
    </form>

    @auth
        <form method="POST" action="{{ route('logout') }}" class="text-center mt-4">
            @csrf
            <button type="submit" class="btn btn-link btn-sm p-0"><i class="fa-solid fa-arrow-right-from-bracket me-1" aria-hidden="true"></i> Sign out</button>
        </form>
    @else
        <p class="small text-center mt-4 mb-0">
            <a href="{{ route('login') }}">Back to sign in</a>
        </p>
    @endauth
</x-layouts.auth>
