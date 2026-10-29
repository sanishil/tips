@props(['subtitle' => 'Assignment cover page', 'href' => null])

<header {{ $attributes->merge(['class' => 'bg-body border-bottom']) }}>
    <div class="container-xl d-flex align-items-center justify-content-between gap-3 py-3">
        <a href="{{ $href ?? route('cover.form') }}" class="text-decoration-none" data-intro-replay><x-brand :subtitle="$subtitle" /></a>
        {{ $slot }}
    </div>
</header>
