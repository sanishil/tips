@props(['code', 'title'])

<x-layouts.app :title="$title" class="d-flex flex-column min-vh-100">
    <x-site-header />

    <main id="main" class="flex-grow-1 container-xl d-flex align-items-center py-5">
        <div class="tw:max-w-lg">
            <p class="text-body-secondary mb-2">Error {{ $code }}</p>
            <h1 class="font-display display-6 mb-3">{{ $title }}</h1>
            <div class="text-body-secondary mb-4">{{ $slot }}</div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('cover.form') }}" class="btn btn-primary"><i class="fa-solid fa-file-lines me-1" aria-hidden="true"></i> Go to the cover page form</a>
                <a href="{{ route('login') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-right-to-bracket me-1" aria-hidden="true"></i> Sign in</a>
            </div>
        </div>
    </main>

    <x-site-footer />
</x-layouts.app>
