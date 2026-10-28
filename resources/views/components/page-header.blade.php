@props(['title', 'subtitle' => null])

<div class="d-flex flex-column flex-sm-row align-items-sm-end justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 font-display mb-1">{{ $title }}</h1>
        @if ($subtitle)
            <p class="text-body-secondary mb-0">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="d-flex flex-wrap gap-2">{{ $slot }}</div>
    @endif
</div>
