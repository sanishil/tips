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

<x-layouts.app :title="$title.' · '.$portalName" class="portal d-flex flex-column min-vh-100">
    <x-site-header :subtitle="$portalName" :href="route($portal.'.dashboard')">
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <x-theme-toggle />
            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
                <i class="fa-solid fa-bars me-1" aria-hidden="true"></i> Menu
            </button>
        </div>
    </x-site-header>

    <aside class="sidebar offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebar-label">
        <div class="offcanvas-header border-bottom">
            <span id="sidebar-label" class="sidebar-brand">
                <img src="{{ asset('img/logo.webp') }}" alt="" aria-hidden="true">
                <span>
                    TIPS EduCover
                    <small>{{ $portalName }}</small>
                </span>
            </span>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close menu"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column p-0">
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
                <a href="{{ route('login', ['signed_out' => 1]) }}" class="nav-link">
                    <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>Sign out
                </a>
                <p class="small text-body-secondary px-2 pt-2 mb-1">You'll be signed out after 10 minutes without activity.</p>
            </div>
        </div>
    </aside>

    <main id="main" class="container-xl py-4 flex-grow-1">
        {{ $slot }}
    </main>

    <x-site-footer />
</x-layouts.app>
