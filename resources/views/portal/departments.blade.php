@php
    $createErrors = $errors->getBag('createDepartment');
    $updateErrors = $errors->getBag('updateDepartment');

    /* After a failed edit, reopen the dialog pointed at the department being edited. */
    $editAction = $updateErrors->any() && old('department_id')
        ? route($portal.'.departments.update', old('department_id'))
        : route($portal.'.departments');
@endphp

<x-layouts.portal :portal="$portal" title="Departments">
    <x-page-header title="Departments" subtitle="Subjects, teachers and students all belong to a department." />

    <x-table-card id="departments-table" title="All departments" :count="$departments->count()" placeholder="Search departments">
        <x-slot:actions>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add-department"><i class="fa-solid fa-plus me-1" aria-hidden="true"></i> Add department</button>
        </x-slot:actions>
        <thead>
            <tr><th>Department</th><th>Head of department</th><th class="text-end">Teachers</th><th>Added</th><th class="text-end" data-export-skip><span class="visually-hidden">Actions</span></th></tr>
        </thead>
        <tbody>
            @forelse ($departments as $department)
                <tr>
                    <td class="fw-semibold">{{ $department->dept_name }}</td>
                    <td>{{ $department->hod }}</td>
                    <td class="text-end">{{ $department->teachers_count }}</td>
                    <td class="text-body-secondary" data-export="{{ $department->created_at->toDateString() }}">{{ $department->created_at->format('j M Y') }}</td>
                    <x-row-actions
                        :record="['department_id' => $department->id, 'dept_name' => $department->dept_name, 'hod' => $department->hod]"
                        :label="$department->dept_name"
                        edit="edit-department"
                        delete="delete-department"
                        :edit-action="route($portal.'.departments.update', $department)"
                        :delete-action="route($portal.'.departments.destroy', $department)"
                    />
                </tr>
            @empty
                <x-empty-state colspan="5" title="No departments yet" icon="fa-building" />
            @endforelse
        </tbody>
    </x-table-card>

    <x-modal id="add-department" icon="fa-plus" title="Add department" submit="Add department" busy="Adding…" :action="route($portal.'.departments.store')" :show-on-load="$createErrors->any()">
        <div>
            <label class="form-label" for="add_dept_name">Name</label>
            <input id="add_dept_name" name="dept_name" @class(['form-control', 'is-invalid' => $createErrors->has('dept_name')]) required minlength="2" maxlength="100" value="{{ $createErrors->any() ? old('dept_name') : '' }}">
            <x-field-error for="dept_name" bag="createDepartment">Give the department a name.</x-field-error>
        </div>
        <div>
            <label class="form-label" for="add_hod">Head of department</label>
            <input id="add_hod" name="hod" @class(['form-control', 'is-invalid' => $createErrors->has('hod')]) required minlength="2" maxlength="100" value="{{ $createErrors->any() ? old('hod') : '' }}">
            <x-field-error for="hod" bag="createDepartment">Who heads this department?</x-field-error>
        </div>
    </x-modal>

    <x-modal id="edit-department" icon="fa-floppy-disk" title="Edit department" submit="Save changes" :action="$editAction" method="PUT" :show-on-load="$updateErrors->any()">
        <input type="hidden" name="department_id" value="{{ $updateErrors->any() ? old('department_id') : '' }}">
        <div>
            <label class="form-label" for="edit_dept_name">Name</label>
            <input id="edit_dept_name" name="dept_name" @class(['form-control', 'is-invalid' => $updateErrors->has('dept_name')]) required minlength="2" maxlength="100" value="{{ $updateErrors->any() ? old('dept_name') : '' }}">
            <x-field-error for="dept_name" bag="updateDepartment">Give the department a name.</x-field-error>
        </div>
        <div>
            <label class="form-label" for="edit_hod">Head of department</label>
            <input id="edit_hod" name="hod" @class(['form-control', 'is-invalid' => $updateErrors->has('hod')]) required minlength="2" maxlength="100" value="{{ $updateErrors->any() ? old('hod') : '' }}">
            <x-field-error for="hod" bag="updateDepartment">Who heads this department?</x-field-error>
        </div>
    </x-modal>

    <x-modal id="delete-department" title="Delete this department?" submit="Delete" busy="Deleting…" danger :action="route($portal.'.departments')" method="DELETE">
        <p class="mb-0"><strong data-record-text="dept_name"></strong> will be removed. This can't be undone. A department that still has teachers can't be deleted.</p>
    </x-modal>
</x-layouts.portal>
