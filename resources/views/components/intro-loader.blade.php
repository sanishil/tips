@props(['name' => 'Tripura Institute of Paramedical Sciences', 'subtitle' => 'Assignment cover pages'])

{{-- Played by resources/js/app.js on page load, and again when a [data-intro-replay] link is clicked. --}}
<div class="intro-loader" data-intro aria-hidden="true">
    <img class="intro-loader-logo" src="{{ asset('img/logo.webp') }}" alt="" data-intro-logo>
    <p class="intro-loader-name font-display">
        <span class="intro-loader-ghost">{{ $name }}</span>
        <span><span data-intro-typed data-text="{{ $name }}"></span><span class="intro-loader-caret" data-intro-caret></span></span>
    </p>
    <p class="intro-loader-subtitle" data-intro-subtitle>{{ $subtitle }}</p>
    <span class="intro-loader-bar"><span data-intro-bar></span></span>
</div>
