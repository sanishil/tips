<footer {{ $attributes->merge(['class' => 'border-top py-4 small text-body-secondary']) }}>
    <div class="container-xl d-flex flex-column flex-sm-row justify-content-between gap-2">
        <p class="mb-0">&copy; {{ now()->year }} Tripura Institute of Paramedical Sciences</p>
        <nav aria-label="Footer">
            <ul class="list-inline mb-0">
                <li class="list-inline-item"><a class="link-secondary" href="{{ route('cover.form') }}">Cover page</a></li>
                <li class="list-inline-item"><a class="link-secondary" href="{{ route('login') }}">Staff sign in</a></li>
                <li class="list-inline-item"><a class="link-secondary" href="{{ route('password.request') }}">Reset password</a></li>
            </ul>
        </nav>
    </div>
</footer>
