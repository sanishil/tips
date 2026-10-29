<x-layouts.auth title="Create an account">
    <x-auth-steps :steps="['Your details', 'Code', 'Password']" :current="1" />

    <h1 class="h3 font-display mb-4">Create an account</h1>

    <form method="POST" action="{{ route('register') }}" class="vstack gap-3" novalidate>
        @csrf
        <div>
            <label class="form-label" for="name">Full name</label>
            <input id="name" name="name" @class(['form-control', 'is-invalid' => $errors->has('name')]) autocomplete="name" required maxlength="100" autofocus value="{{ old('name') }}">
            <x-field-error for="name">Enter your name.</x-field-error>
        </div>

        <div>
            <label class="form-label" for="email">Email</label>
            <input id="email" name="email" type="email" @class(['form-control', 'is-invalid' => $errors->has('email')]) autocomplete="email" required maxlength="255" value="{{ old('email') }}">
            <x-field-error for="email">Enter your email address.</x-field-error>
        </div>

        <x-recaptcha />

        <button type="submit" class="btn btn-success w-100 mt-2" data-busy="Sending code…"><i class="fa-solid fa-paper-plane me-1" aria-hidden="true"></i> Send code</button>
    </form>

    <p class="small text-center mt-4 mb-0">
        Already have an account? <a href="{{ route('login') }}">Sign in</a>
    </p>
</x-layouts.auth>
