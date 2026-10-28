@props(['title', 'icon' => 'fa-folder-open', 'colspan' => null])

@if ($colspan)
    <tr data-empty-row>
        <td colspan="{{ $colspan }}" class="py-5">
@endif

<div {{ $attributes->merge(['class' => 'text-center text-body-secondary py-4 px-3 tw:max-w-sm mx-auto']) }}>
    <i class="fa-regular {{ $icon }} fs-3 mb-3 opacity-50" aria-hidden="true"></i>
    <p class="fw-semibold text-body mb-1">{{ $title }}</p>
    @if ($slot->isNotEmpty())
        <div class="small text-wrap">{{ $slot }}</div>
    @endif
</div>

@if ($colspan)
        </td>
    </tr>
@endif
