@props(['user'])

{{-- The user's photo, or their initials when they haven't added one. --}}
<span {{ $attributes->merge(['class' => 'avatar']) }}>
    @if ($user->photo_url)
        <img src="{{ $user->photo_url }}" alt="">
    @else
        {{ $user->initials }}
    @endif
</span>
