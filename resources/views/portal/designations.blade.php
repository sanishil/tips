@php
    $createErrors = $errors->getBag('createDesignation');
    $updateErrors = $errors->getBag('updateDesignation');

    /* After a failed edit, reopen the dialog pointed at the designation being edited. */
    $editAction = $updateErrors->any() && old('designation_id')
        ? route($portal.'.designations.update', old('designation_id'))
        : route($portal.'.designations');
@endphp

<x-layouts.portal :portal="$portal" title="Designations">
    <x-page-header title="Designations" subtitle="Teachers pick their designation from this list; it's printed under their name on the cover page." />

    <x-table-card id="designations-table" title="All designations" :count="$designations->count()" placeholder="Search designations">
        <x-slot:actions>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add-designation"><i class="fa-solid fa-plus me-1" aria-hidden="true"></i> Add designation</button>
        </x-slot:actions>
        <thead>
            <tr><th>Designation</th><th class="text-end">Teachers</th><th>Added</th><th class="text-end" data-export-skip><span class="visually-hidden">Actions</span></th></tr>
        </thead>
        <tbody>
            @forelse ($designations as $designation)
                <tr>
                    <td class="fw-semibold">{{ $designation->name }}</td>
                    <td class="text-end">{{ $designation->teachers_count }}</td>
                    <td class="text-body-secondary" data-export="{{ $designation->created_at->toDateString() }}">{{ $designation->created_at->format('j M Y') }}</td>
                    <x-row-actions
                        :record="['designation_id' => $designation->id, 'name' => $designation->name]"
                        :label="$designation->name"
                        edit="edit-designation"
                        delete="delete-designation"
                        :edit-action="route($portal.'.designations.update', $designation)"
                        :delete-action="route($portal.'.designations.destroy', $designation)"
                    />
                </tr>
            @empty
                <x-empty-state colspan="4" title="No designations yet" icon="fa-id-badge" />
            @endforelse
        </tbody>
    </x-table-card>

    <x-modal id="add-designation" icon="fa-plus" title="Add designation" submit="Add designation" busy="Adding…" :action="route($portal.'.designations.store')" :show-on-load="$createErrors->any()">
        <div>
            <label class="form-label" for="add_designation_name">Designation</label>
            <input id="add_designation_name" name="name" @class(['form-control', 'is-invalid' => $createErrors->has('name')]) required minlength="2" maxlength="60" placeholder="e.g. Assistant Professor" value="{{ $createErrors->any() ? old('name') : '' }}">
            <x-field-error for="name" bag="createDesignation">Enter a designation, e.g. Assistant Professor.</x-field-error>
        </div>
    </x-modal>

    <x-modal id="edit-designation" icon="fa-floppy-disk" title="Edit designation" submit="Save changes" :action="$editAction" method="PUT" :show-on-load="$updateErrors->any()">
        <input type="hidden" name="designation_id" value="{{ $updateErrors->any() ? old('designation_id') : '' }}">
        <div>
            <label class="form-label" for="edit_designation_name">Designation</label>
            <input id="edit_designation_name" name="name" @class(['form-control', 'is-invalid' => $updateErrors->has('name')]) required minlength="2" maxlength="60" value="{{ $updateErrors->any() ? old('name') : '' }}">
            <x-field-error for="name" bag="updateDesignation">Enter a designation, e.g. Assistant Professor.</x-field-error>
            <div class="form-text">Teachers with this designation show the new name straight away.</div>
        </div>
    </x-modal>

    <x-modal id="delete-designation" title="Delete this designation?" submit="Delete" busy="Deleting…" danger :action="route($portal.'.designations')" method="DELETE">
        <p class="mb-0"><strong data-record-text="name"></strong> will be removed. A designation that teachers still use can't be deleted.</p>
    </x-modal>
</x-layouts.portal>
