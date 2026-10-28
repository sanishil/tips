@props(['for'])

{{-- Server-side errors win; the slot is the message shown by browser validation. --}}
<div @class(['invalid-feedback', 'd-block' => $errors->has($for)])>{{ $errors->first($for) ?: $slot }}</div>
