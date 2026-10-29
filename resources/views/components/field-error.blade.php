@props(['for', 'bag' => 'default'])

{{-- Server-side errors win; the slot is the message shown by browser validation. `bag` picks a named error bag (e.g. createDepartment). --}}
@php($bagErrors = $errors->getBag($bag))
<div @class(['invalid-feedback', 'd-block' => $bagErrors->has($for)])>{{ $bagErrors->first($for) ?: $slot }}</div>
