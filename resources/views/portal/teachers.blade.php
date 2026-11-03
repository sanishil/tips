@php
    $createErrors = $errors->getBag('createTeacher');
    $updateErrors = $errors->getBag('updateTeacher');

    /* After a failed edit, reopen the dialog pointed at the teacher being edited. */
    $editAction = $updateErrors->any() && old('teacher_id')
        ? route($portal.'.teachers.update', old('teacher_id'))
        : route($portal.'.teachers');

    $dialogs = [
        'add' => ['title' => 'Add teacher', 'submit' => 'Add teacher', 'icon' => 'fa-user-plus', 'errors' => $createErrors, 'bag' => 'createTeacher'],
        'edit' => ['title' => 'Edit teacher', 'submit' => 'Save changes', 'icon' => 'fa-floppy-disk', 'errors' => $updateErrors, 'bag' => 'updateTeacher'],
    ];
@endphp

<x-layouts.portal :portal="$portal" title="Teachers">
    <x-page-header title="Teachers" />

    <x-table-card id="teachers-table" title="All teachers" :count="$teachers->count()" placeholder="Search teachers">
        <x-slot:actions>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add-teacher"><i class="fa-solid fa-user-plus me-1" aria-hidden="true"></i> Add teacher</button>
        </x-slot:actions>
        <x-slot:filters>
            <select class="form-select form-select-sm tw:sm:w-auto" aria-label="Department" data-table-filter="teachers-table" data-filter-key="dept">
                <option value="">All departments</option>
                @foreach ($departments as $department)
                    <option>{{ $department->dept_name }}</option>
                @endforeach
            </select>
        </x-slot:filters>

        <thead>
            <tr><th>Name</th><th>Designations</th><th>Department</th><th data-export-skip><span class="visually-hidden">Actions</span></th></tr>
        </thead>
        <tbody>
            @forelse ($teachers as $teacher)
                <tr data-dept="{{ $teacher->department->dept_name }}">
                    <td class="fw-semibold">{{ $teacher->name }}</td>
                    <td>{{ $teacher->designationTitle() }}</td>
                    <td>{{ $teacher->department->dept_name }}</td>
                    <x-row-actions
                        :record="[
                            'teacher_id' => $teacher->id,
                            'name' => $teacher->name,
                            'designation_ids[]' => $teacher->designations->map(fn ($designation): string => (string) $designation->id)->all(),
                            'department_id' => (string) $teacher->department_id,
                        ]"
                        :label="$teacher->name"
                        edit="edit-teacher"
                        delete="delete-teacher"
                        :edit-action="route($portal.'.teachers.update', $teacher)"
                        :delete-action="route($portal.'.teachers.destroy', $teacher)"
                    />
                </tr>
            @empty
                <x-empty-state colspan="4" title="No teachers yet" icon="fa-user" />
            @endforelse
        </tbody>
    </x-table-card>

    @foreach ($dialogs as $mode => $dialog)
        @php($bagErrors = $dialog['errors'])
        <x-modal
            :id="$mode.'-teacher'"
            :icon="$dialog['icon']"
            :title="$dialog['title']"
            :submit="$dialog['submit']"
            :action="$mode === 'add' ? route($portal.'.teachers.store') : $editAction"
            :method="$mode === 'add' ? 'POST' : 'PUT'"
            :show-on-load="$bagErrors->any()"
        >
            @if ($mode === 'edit')
                <input type="hidden" name="teacher_id" value="{{ $bagErrors->any() ? old('teacher_id') : '' }}">
            @endif
            <div>
                <label class="form-label" for="{{ $mode }}_teacher_name">Full name</label>
                <input id="{{ $mode }}_teacher_name" name="name" @class(['form-control', 'is-invalid' => $bagErrors->has('name')]) required minlength="2" maxlength="100" value="{{ $bagErrors->any() ? old('name') : '' }}">
                <x-field-error for="name" :bag="$dialog['bag']">Enter the teacher's name, including Dr. if they use it.</x-field-error>
            </div>
            <div>
                <label class="form-label" for="{{ $mode }}_teacher_dept">Department</label>
                <select id="{{ $mode }}_teacher_dept" name="department_id" @class(['form-select', 'is-invalid' => $bagErrors->has('department_id')]) required>
                    <option value="">Choose…</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected($bagErrors->any() && (string) old('department_id') === (string) $department->id)>{{ $department->dept_name }}</option>
                    @endforeach
                </select>
                <x-field-error for="department_id" :bag="$dialog['bag']">Choose the teacher's department.</x-field-error>
                @if ($departments->isEmpty())
                    <div class="form-text">No departments yet. <a href="{{ route($portal.'.departments') }}">Add one first</a>.</div>
                @endif
            </div>
            <fieldset>
                <legend class="form-label fs-6 mb-2">Designations <span class="text-body-secondary fw-normal">(tick all that apply)</span></legend>
                <div @class(['border rounded px-3 py-2 overflow-auto tw:max-h-48', 'border-danger' => $bagErrors->has('designation_ids*')])>
                    @forelse ($designations as $designation)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="designation_ids[]" value="{{ $designation->id }}" id="{{ $mode }}_designation_{{ $designation->id }}"
                                   @checked($bagErrors->any() && in_array((string) $designation->id, array_map('strval', (array) old('designation_ids', [])), true))>
                            <label class="form-check-label" for="{{ $mode }}_designation_{{ $designation->id }}">{{ $designation->name }}</label>
                        </div>
                    @empty
                        <p class="small text-body-secondary mb-0">No designations yet. <a href="{{ route($portal.'.designations') }}">Add one first</a>.</p>
                    @endforelse
                </div>
                <x-field-error for="designation_ids*" :bag="$dialog['bag']">Choose at least one designation.</x-field-error>
                <div class="form-text">Several are joined on the cover page, e.g. “Lecturer(SG-I) & Principal (i/c)”.</div>
            </fieldset>
        </x-modal>
    @endforeach

    <x-modal id="delete-teacher" icon="fa-user-minus" title="Remove this teacher?" submit="Remove" busy="Removing…" danger :action="route($portal.'.teachers')" method="DELETE">
        <p class="mb-0"><strong data-record-text="name"></strong> will no longer appear in the student form. Cover pages already printed aren't affected.</p>
    </x-modal>
</x-layouts.portal>
