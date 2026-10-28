@props(['record', 'label', 'edit' => null, 'delete' => null])

<td class="cell-actions text-end" data-export-skip>
    <div class="d-inline-flex gap-1">
        {{ $slot }}
        @if ($edit)
            <button type="button" class="btn btn-sm btn-icon" data-bs-toggle="modal" data-bs-target="#{{ $edit }}" data-record="{{ json_encode($record) }}" aria-label="Edit {{ $label }}" title="Edit">
                <i class="fa-solid fa-pen" aria-hidden="true"></i>
            </button>
        @endif
        @if ($delete)
            <button type="button" class="btn btn-sm btn-icon btn-icon-danger" data-bs-toggle="modal" data-bs-target="#{{ $delete }}" data-record="{{ json_encode($record) }}" aria-label="Delete {{ $label }}" title="Delete">
                <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
            </button>
        @endif
    </div>
</td>
