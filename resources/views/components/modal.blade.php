@props(['id', 'title', 'size' => null, 'submit' => 'Save', 'busy' => 'Saving…', 'message' => null, 'danger' => false])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}-title" aria-hidden="true">
    <div @class(['modal-dialog modal-dialog-centered', $size])>
        <form method="POST" class="modal-content" data-demo-form="{{ $message }}" novalidate>
            @csrf
            <div class="modal-header">
                <h2 class="modal-title h5 font-display" id="{{ $id }}-title">{{ $title }}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body vstack gap-3">
                {{ $slot }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" data-busy="{{ $busy }}" @class(['btn', 'btn-danger' => $danger, 'btn-primary' => ! $danger])>{{ $submit }}</button>
            </div>
        </form>
    </div>
</div>
