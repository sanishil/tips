@props(['title'])

<x-layouts.app :title="$title" :themeable="false" class="intake d-flex flex-column min-vh-100">
    <x-site-header subtitle="Portal">
        <a href="{{ route('cover.form') }}" class="btn btn-sm btn-outline-primary flex-shrink-0">
            <i class="fa-solid fa-file-lines me-1" aria-hidden="true"></i> Cover page
        </a>
    </x-site-header>

    <main id="main" class="auth-main flex-grow-1 d-flex align-items-center justify-content-center px-3 py-5">
        <div class="auth-card brand-form w-100 tw:max-w-[26rem]">
            {{ $slot }}
        </div>
    </main>

    <x-site-footer />
</x-layouts.app>
