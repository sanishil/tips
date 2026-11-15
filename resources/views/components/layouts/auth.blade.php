@props(['title'])

<x-layouts.app :title="$title" :themeable="false" class="intake d-flex flex-column min-vh-100">
    <x-site-header subtitle="Portal">
        <a href="{{ route('cover.form') }}" class="btn btn-sm btn-outline-primary flex-shrink-0">
            <i class="fa-solid fa-file-lines me-1" aria-hidden="true"></i> Cover page
        </a>
    </x-site-header>

    <main id="main" class="auth-main flex-grow-1">
        {{-- Desktop only: what the site makes, shown as a sheet like the ones it prints. --}}
        <aside class="auth-aside d-none d-lg-flex">
            <div class="auth-aside-inner">
                <h2 class="font-display auth-aside-title">Assignment covers, ready to print.</h2>
                <p class="intake-muted mb-0">Assignments, case studies, practical records and project reports, all on the college's own A4 cover sheet.</p>

                <div class="auth-sheets" aria-hidden="true">
                    <div class="auth-sheet auth-sheet-back"></div>
                    <div class="auth-sheet">
                        <p class="fw-bold mb-0">Assignment</p>
                        <p class="auth-sheet-college fw-bold mb-0">Tripura Institute of Paramedical Sciences</p>
                        <img src="{{ asset('img/logo.webp') }}" alt="">
                        <p class="fw-bold text-uppercase mb-0">Agartala, Tripura</p>
                        <p class="mb-0"><strong>Subject Title:</strong> Human Anatomy</p>
                        <p class="mb-0"><strong>Subject Code:</strong> MLT-101</p>
                        <span class="auth-sheet-lines"><i></i><i></i><i></i></span>
                    </div>
                </div>
            </div>
        </aside>

        <div class="auth-form brand-form">
            {{ $slot }}
        </div>
    </main>

    <x-site-footer />
</x-layouts.app>
