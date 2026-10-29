@props(['title', 'subtitle' => null])

{{-- The page's <h1> is in the portal top bar ($title there); this only adds the page's short note. --}}
@if ($subtitle)
    <p class="page-note">{{ $subtitle }}</p>
@endif
