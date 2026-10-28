@props(['title'])

<x-layouts.app :title="$title" class="d-flex flex-column min-vh-100">
    <header class="container-xl d-flex align-items-center justify-content-between py-3">
        <a href="{{ route('cover.form') }}" class="text-decoration-none"><x-brand /></a>
        <x-theme-toggle />
    </header>

    <main id="main" class="flex-grow-1 d-flex align-items-start align-items-sm-center justify-content-center px-3 py-4">
        <div class="card shadow-sm w-100 tw:max-w-[25rem]">
            <div class="card-body p-4 p-sm-5">
                {{ $slot }}
            </div>
        </div>
    </main>

    <x-site-footer />
</x-layouts.app>
