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
    <x-page-header title="Teachers" subtitle="Students pick from this list under “Submitted to”." />

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
            <tr><th>Name</th><th>Designation</th><th>Department</th><th data-export-skip><span class="visually-hidden">Actions</span></th></tr>
        </thead>
        <tbody>
            @forelse ($teachers as $teacher)
                <tr data-dept="{{ $teacher->department->dept_name }}">
                    <td class="fw-semibold">{{ $teacher->name }}</td>
                    <td>{{ $teacher->designation->name }}</td>
                    <td>{{ $teacher->department->dept_name }}</td>
                    <x-row-actions
                        :record="['teacher_id' => $teacher->id, 'name' => $teacher->name, 'designation_id' => (string) $teacher->designation_id, 'department_id' => (string) $teacher->department_id]"
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
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label" for="{{ $mode }}_designation">Designation</label>
                    <select id="{{ $mode }}_designation" name="designation_id" @class(['form-select', 'is-invalid' => $bagErrors->has('designation_id')]) required>
                        <option value="">Choose…</option>
                        @foreach ($designations as $designation)
                            <option value="{{ $designation->id }}" @selected($bagErrors->any() && (string) old('designation_id') === (string) $designation->id)>{{ $designation->name }}</option>
                        @endforeach
                    </select>
                    <x-field-error for="designation_id" :bag="$dialog['bag']">Choose the teacher's designation.</x-field-error>
                    @if ($designations->isEmpty())
                        <div class="form-text">No designations yet. <a href="{{ route($portal.'.designations') }}">Add one first</a>.</div>
                    @endif
                </div>
                <div class="col-sm-6">
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
            </div>
        </x-modal>
    @endforeach

    <x-modal id="delete-teacher" icon="fa-user-minus" title="Remove this teacher?" submit="Remove" busy="Removing…" danger :action="route($portal.'.teachers')" method="DELETE">
        <p class="mb-0"><strong data-record-text="name"></strong> will no longer appear in the student form. Cover pages already printed aren't affected.</p>
    </x-modal>
</x-layouts.portal>
