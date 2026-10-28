@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Make and print assignment cover pages for Tripura Institute of Paramedical Sciences.">
        <meta name="theme-color" content="#0f5c5e">

        <title>{{ $title ? $title.' — ' : '' }}{{ config('app.name') }}</title>

        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

        <script>
            try {
                if (localStorage.getItem('tips_theme') === 'dark') {
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                }
            } catch (e) {}
        </script>

        @fonts
        @vite(['resources/css/bootstrap.scss', 'resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body {{ $attributes }}>
        <a href="#main" class="visually-hidden-focusable position-absolute top-0 start-0 m-2 btn btn-primary z-3">Skip to main content</a>

        {{ $slot }}

        @if (session('status'))
            <template data-toast="success">{{ session('status') }}</template>
        @endif
    </body>
</html>
