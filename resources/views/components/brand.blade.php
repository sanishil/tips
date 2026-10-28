@props(['subtitle' => 'Assignment cover pages'])

<span {{ $attributes->merge(['class' => 'd-inline-flex align-items-center gap-2 text-reset text-decoration-none']) }}>
    <svg class="brand-mark" viewBox="0 0 32 32" aria-hidden="true">
        <rect width="32" height="32" rx="6" fill="#0f5c5e" />
        <path d="M13 6.5h6v6.5h6.5v6H19v6.5h-6V19H6.5v-6H13z" fill="#fff" />
    </svg>
    <span class="lh-sm text-start">
        <span class="d-block font-display fw-semibold text-body-emphasis">
            <span class="d-sm-none">TIPS</span><span class="d-none d-sm-inline">Tripura Institute of Paramedical Sciences</span>
        </span>
        @if ($subtitle)
            <span class="d-block small text-body-secondary">{{ $subtitle }}</span>
        @endif
    </span>
</span>
