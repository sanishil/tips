@php
    $createErrors = $errors->getBag('createAssignmentType');
    $updateErrors = $errors->getBag('updateAssignmentType');

    /* After a failed edit, reopen the dialog pointed at the assignment type being edited. */
    $editAction = $updateErrors->any() && old('assignment_type_id')
        ? route($portal.'.assignment-types.update', old('assignment_type_id'))
        : route($portal.'.assignment-types');
@endphp

<x-layouts.portal :portal="$portal" title="Assignment types">
    <x-page-header title="Assignment types" />

    <x-table-card id="assignment-types-table" title="All assignment types" :count="$assignmentTypes->count()" placeholder="Search assignment types">
        <x-slot:actions>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add-assignment-type"><i class="fa-solid fa-plus me-1" aria-hidden="true"></i> Add assignment type</button>
        </x-slot:actions>
        <thead>
            <tr><th>Assignment type</th><th>Added</th><th class="text-end" data-export-skip><span class="visually-hidden">Actions</span></th></tr>
        </thead>
        <tbody>
            @forelse ($assignmentTypes as $assignmentType)
                <tr>
                    <td class="fw-semibold">{{ $assignmentType->name }}</td>
                    <td class="text-body-secondary" data-export="{{ $assignmentType->created_at->toDateString() }}">{{ $assignmentType->created_at->format('j M Y') }}</td>
                    <x-row-actions
                        :record="['assignment_type_id' => $assignmentType->id, 'name' => $assignmentType->name]"
                        :label="$assignmentType->name"
                        edit="edit-assignment-type"
                        delete="delete-assignment-type"
                        :edit-action="route($portal.'.assignment-types.update', $assignmentType)"
                        :delete-action="route($portal.'.assignment-types.destroy', $assignmentType)"
                    />
                </tr>
            @empty
                <x-empty-state colspan="3" title="No assignment types yet" icon="fa-file-lines" />
            @endforelse
        </tbody>
    </x-table-card>

    <x-modal id="add-assignment-type" icon="fa-plus" title="Add assignment type" submit="Add assignment type" busy="Adding…" :action="route($portal.'.assignment-types.store')" :show-on-load="$createErrors->any()">
        <div>
            <label class="form-label" for="add_assignment_type_name">Assignment type</label>
            <input id="add_assignment_type_name" name="name" @class(['form-control', 'is-invalid' => $createErrors->has('name')]) required minlength="2" maxlength="60" placeholder="e.g. Assignment 1" value="{{ $createErrors->any() ? old('name') : '' }}">
            <x-field-error for="name" bag="createAssignmentType">Enter an assignment type, e.g. Assignment 1.</x-field-error>
            <div class="form-text">Students pick this under “This is a…” on the cover page.</div>
        </div>
    </x-modal>

    <x-modal id="edit-assignment-type" icon="fa-floppy-disk" title="Edit assignment type" submit="Save changes" :action="$editAction" method="PUT" :show-on-load="$updateErrors->any()">
        <input type="hidden" name="assignment_type_id" value="{{ $updateErrors->any() ? old('assignment_type_id') : '' }}">
        <div>
            <label class="form-label" for="edit_assignment_type_name">Assignment type</label>
            <input id="edit_assignment_type_name" name="name" @class(['form-control', 'is-invalid' => $updateErrors->has('name')]) required minlength="2" maxlength="60" value="{{ $updateErrors->any() ? old('name') : '' }}">
            <x-field-error for="name" bag="updateAssignmentType">Enter an assignment type, e.g. Assignment 1.</x-field-error>
        </div>
    </x-modal>

    <x-modal id="delete-assignment-type" title="Delete this assignment type?" submit="Delete" busy="Deleting…" danger :action="route($portal.'.assignment-types')" method="DELETE">
        <p class="mb-0"><strong data-record-text="name"></strong> will be removed from the cover page.</p>
    </x-modal>
</x-layouts.portal>
