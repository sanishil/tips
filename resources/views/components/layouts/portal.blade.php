@props(['portal', 'title'])

@php
    $items = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fa-gauge'],
        ['route' => 'students', 'label' => 'Students', 'icon' => 'fa-user-graduate'],
        ['route' => 'departments', 'label' => 'Departments', 'icon' => 'fa-building-columns'],
        ['route' => 'subjects', 'label' => 'Subjects', 'icon' => 'fa-book'],
        ['route' => 'teachers', 'label' => 'Teachers', 'icon' => 'fa-chalkboard-user'],
    ];

    if ($portal === 'admin') {
        $items[] = ['route' => 'users', 'label' => 'User accounts', 'icon' => 'fa-users-gear'];
        $items[] = ['route' => 'active-users', 'label' => 'Signed in now', 'icon' => 'fa-circle-dot'];
    }

    $items[] = ['route' => 'profile', 'label' => 'My profile', 'icon' => 'fa-circle-user'];

    $user = auth()->user();
    $portalName = $portal === 'admin' ? 'Admin' : 'Tech portal';
@endphp

<x-layouts.app :title="$title.' · '.$portalName" :themeable="false" class="portal intake">
    {{-- Apply the remembered folded sidebar before it paints (see the sidebar toggle in app.js). --}}
    <script>
        try {
            if (localStorage.getItem('tips_sidebar') === 'collapsed') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        } catch (e) {}
    </script>

    {{-- Browsers that support it load a portal page while the pointer rests on its link, so the click opens it at once. --}}
    <script type="speculationrules">
        {
            "prefetch": [{
                "where": { "href_matches": "/{{ $portal }}/*" },
                "eagerness": "moderate"
            }, {
                "where": { "href_matches": "/{{ $portal }}" },
                "eagerness": "moderate"
            }]
        }
    </script>

    {{-- Institute banner: scrolls away; the top bar below stays. --}}
    <header class="portal-banner">
        <a href="{{ route($portal.'.dashboard') }}" class="portal-banner-brand">
            <img src="{{ asset('img/logo.webp') }}" alt="" aria-hidden="true">
            <span>
                <span class="portal-banner-name">Tripura Institute of Paramedical Sciences</span>
                <span class="portal-banner-tagline">Agartala, Tripura · Assignment cover pages</span>
            </span>
        </a>
        <span class="portal-banner-date d-none d-md-block">{{ now()->format('l, j F Y') }}</span>
    </header>

    <div class="portal-shell">
        {{-- Desktop: always there, folds to icons. Below 992px: a slide-in panel. --}}
        <aside class="portal-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" aria-label="Main menu">
            <div class="portal-sidebar-inner">
                <div class="portal-sidebar-brand">
                    <img src="{{ asset('img/logo.webp') }}" alt="" aria-hidden="true">
                    <span class="nav-label">
                        <strong>TIPS {{ $portal === 'admin' ? 'Admin' : 'Tech' }}</strong>
                        <small>Control panel</small>
                    </span>
                    <button type="button" class="btn-close btn-close-white ms-auto d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close menu"></button>
                </div>

                <nav class="flex-grow-1 overflow-y-auto py-3" aria-label="Main">
                    <ul class="nav flex-column">
                        @foreach ($items as $item)
                            @php($isActive = request()->routeIs($portal.'.'.$item['route']))
                            <li class="nav-item">
                                <a href="{{ route($portal.'.'.$item['route']) }}" @class(['nav-link', 'active' => $isActive]) @if ($isActive) aria-current="page" @endif title="{{ $item['label'] }}">
                                    <i class="fa-solid {{ $item['icon'] }} fa-fw" aria-hidden="true"></i><span class="nav-label">{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="portal-content">
            <div class="portal-topbar">
                <button type="button" class="btn btn-icon fs-5" data-sidebar-toggle aria-controls="sidebar" aria-expanded="true" aria-label="Show or hide the menu" title="Menu">
                    <i class="fa-solid fa-bars fa-fw" aria-hidden="true"></i>
                </button>
                <h1 class="portal-title">{{ $title }}</h1>
                <div class="ms-auto d-flex align-items-center gap-3 flex-shrink-0">
                    <a href="{{ route($portal.'.profile') }}" class="d-flex align-items-center gap-2 text-decoration-none text-reset" title="My profile">
                        <span class="avatar">{{ $user->initials }}</span>
                        <span class="lh-sm d-none d-md-block">
                            <span class="d-block fw-semibold">{{ $user->name }}</span>
                            <small class="text-body-secondary">{{ $user->role->label() }}</small>
                        </span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-right-from-bracket me-1" aria-hidden="true"></i> Sign out
                        </button>
                    </form>
                </div>
            </div>

            <main id="main" class="portal-main brand-form">
                {{ $slot }}
            </main>

            <x-site-footer />
        </div>
    </div>
</x-layouts.app>
