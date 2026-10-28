@props(['portal', 'title'])

@php
    $sections = [
        null => [
            ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fa-gauge'],
        ],
        'Records' => [
            ['route' => 'students', 'label' => 'Students', 'icon' => 'fa-user-graduate'],
            ['route' => 'departments', 'label' => 'Departments', 'icon' => 'fa-building-columns'],
            ['route' => 'subjects', 'label' => 'Subjects', 'icon' => 'fa-book'],
            ['route' => 'teachers', 'label' => 'Teachers', 'icon' => 'fa-chalkboard-user'],
        ],
    ];

    if ($portal === 'admin') {
        $sections['Accounts'] = [
            ['route' => 'users', 'label' => 'User accounts', 'icon' => 'fa-users-gear'],
            ['route' => 'active-users', 'label' => 'Signed in now', 'icon' => 'fa-circle-dot'],
        ];
    }

    $user = \App\Support\DemoData::currentUser($portal);
    $portalName = $portal === 'admin' ? 'Admin' : 'Student portal';
@endphp

<x-layouts.app :title="$title.' · '.$portalName">
    <aside class="sidebar bg-body border-end">
        <div class="offcanvas-lg offcanvas-start h-100" tabindex="-1" id="sidebar" aria-labelledby="sidebar-label">
            <div class="offcanvas-header border-bottom">
                <x-brand id="sidebar-label" :subtitle="$portalName" />
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close menu"></button>
            </div>

            <div class="offcanvas-body d-flex flex-column h-100 p-0">
                <a href="{{ route($portal.'.dashboard') }}" class="d-none d-lg-block px-3 py-3 border-bottom text-decoration-none">
                    <span class="d-flex align-items-center gap-2">
                        <svg class="brand-mark" viewBox="0 0 32 32" aria-hidden="true"><rect width="32" height="32" rx="6" fill="#0f5c5e" /><path d="M13 6.5h6v6.5h6.5v6H19v6.5h-6V19H6.5v-6H13z" fill="#fff" /></svg>
                        <span class="lh-sm">
                            <span class="d-block font-display fw-semibold text-body-emphasis">TIPS EduCover</span>
                            <span class="d-block small text-body-secondary">{{ $portalName }}</span>
                        </span>
                    </span>
                </a>

                <nav class="flex-grow-1 overflow-y-auto px-2 py-2" aria-label="Main">
                    @foreach ($sections as $heading => $items)
                        @if ($heading)
                            <p class="nav-heading">{{ $heading }}</p>
                        @endif
                        <ul class="nav flex-column gap-1">
                            @foreach ($items as $item)
                                @php($isActive = request()->routeIs($portal.'.'.$item['route']))
                                <li class="nav-item">
                                    <a href="{{ route($portal.'.'.$item['route']) }}" @class(['nav-link', 'active' => $isActive]) @if ($isActive) aria-current="page" @endif>
                                        <i class="fa-solid {{ $item['icon'] }}" aria-hidden="true"></i>{{ $item['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                </nav>

                <div class="border-top p-2">
                    <a href="{{ route($portal.'.profile') }}" @class(['nav-link', 'active' => request()->routeIs($portal.'.profile')])>
                        <span class="avatar">{{ $user['initials'] }}</span>
                        <span class="lh-sm text-truncate">
                            <span class="d-block text-truncate">{{ $user['name'] }}</span>
                            <span class="d-block small text-body-secondary">My profile</span>
                        </span>
                    </a>
                    <div class="d-flex align-items-center">
                        <a href="{{ route('login', ['signed_out' => 1]) }}" class="nav-link flex-grow-1">
                            <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>Sign out
                        </a>
                        <x-theme-toggle class="d-none d-lg-inline-block" />
                    </div>
                    <p class="small text-body-secondary px-2 pt-2 mb-1">You'll be signed out after 10 minutes without activity.</p>
                </div>
            </div>
        </div>
    </aside>

    <div class="with-sidebar d-flex flex-column min-vh-100">
        <header class="navbar bg-body border-bottom px-3 d-lg-none sticky-top">
            <button type="button" class="btn btn-icon" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Open menu">
                <i class="fa-solid fa-bars fa-lg" aria-hidden="true"></i>
            </button>
            <a href="{{ route($portal.'.dashboard') }}" class="text-decoration-none font-display fw-semibold text-body-emphasis">TIPS EduCover</a>
            <x-theme-toggle />
        </header>

        <main id="main" class="container-xxl px-3 px-sm-4 py-4 flex-grow-1">
            {{ $slot }}
        </main>

        <footer class="container-xxl px-3 px-sm-4 py-3 small text-body-secondary border-top">
            &copy; {{ now()->year }} Tripura Institute of Paramedical Sciences
        </footer>
    </div>
</x-layouts.app>
