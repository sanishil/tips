@props(['subtitle' => 'Assignment cover pages'])

<span {{ $attributes->merge(['class' => 'd-inline-flex align-items-center gap-2 text-reset text-decoration-none']) }}>
    <img class="brand-mark" src="{{ asset('img/logo.webp') }}" alt="" aria-hidden="true">
    <span class="lh-sm text-start">
        <span class="d-block font-display fw-semibold text-body-emphasis">Tripura Institute of Paramedical Sciences</span>
        @if ($subtitle)
            <span class="d-block small text-body-secondary">{{ $subtitle }}</span>
        @endif
    </span>
</span>
