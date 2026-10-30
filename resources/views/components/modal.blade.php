@props(['id', 'title', 'size' => null, 'submit' => 'Save', 'busy' => 'Saving…', 'message' => null, 'variant' => 'success', 'icon' => null, 'danger' => false, 'action' => null, 'method' => 'POST', 'showOnLoad' => false])

{{--
| With an action the form posts to the server; without one it's a design-only demo form. showOnLoad reopens it after a failed submit.
| `method` (PUT, DELETE) is sent as Laravel's _method field. A row button's data-action replaces the action when it opens (app.js).
| Submit colour follows the action: success (add, save, send), warning (resets), danger (delete, deactivate); `danger` is short for variant="danger".
| `icon` is a Font Awesome name; without one the colour picks it (check, key, trash can).
--}}
@php($variant = $danger ? 'danger' : $variant)
@php($icon ??= ['success' => 'fa-check', 'warning' => 'fa-key', 'danger' => 'fa-trash-can'][$variant] ?? 'fa-check')
<div class="modal fade" id="{{ $id }}" tabindex="-1" data-bs-backdrop="static" aria-labelledby="{{ $id }}-title" aria-hidden="true" @if ($showOnLoad) data-show-on-load @endif>
    <div @class(['modal-dialog modal-dialog-centered', $size])>
        <form method="POST" class="modal-content" @if ($action) action="{{ $action }}" @else data-demo-form="{{ $message }}" @endif novalidate>
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif
            <div class="modal-header">
                <h2 class="modal-title h5 font-display" id="{{ $id }}-title">{{ $title }}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body vstack gap-3">
                {{ $slot }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="fa-solid fa-xmark me-1" aria-hidden="true"></i> Cancel</button>
                <button type="submit" data-busy="{{ $busy }}" class="btn btn-{{ $variant }}"><i class="fa-solid {{ $icon }} me-1" aria-hidden="true"></i> {{ $submit }}</button>
            </div>
        </form>
    </div>
</div>
