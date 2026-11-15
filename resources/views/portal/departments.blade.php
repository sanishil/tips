@php
    $createErrors = $errors->getBag('createDepartment');
    $updateErrors = $errors->getBag('updateDepartment');

    /* After a failed edit, reopen the dialog pointed at the department being edited. */
    $editAction = $updateErrors->any() && old('department_id')
        ? route($portal.'.departments.update', old('department_id'))
        : route($portal.'.departments');
@endphp

<x-layouts.portal :portal="$portal" title="Departments">
    <x-page-header title="Departments" />

    <x-table-card id="departments-table" title="All departments" :count="$departments->count()" placeholder="Search departments">
        <x-slot:actions>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add-department"><i class="fa-solid fa-plus me-1" aria-hidden="true"></i> Add department</button>
        </x-slot:actions>
        <thead>
            <tr><th>Department</th><th>Course</th><th>Sub-course</th><th class="text-end">Teachers</th><th class="text-end">Subjects</th><th class="text-end" data-export-skip><span class="visually-hidden">Actions</span></th></tr>
        </thead>
        <tbody>
            @forelse ($departments as $department)
                <tr>
                    <td class="fw-semibold">{{ $department->dept_name }}</td>
                    <td>{{ $department->course?->name ?? '—' }}</td>
                    <td>{{ $department->subCourse?->name ?? '—' }}</td>
                    <td class="text-end">{{ $department->teachers_count }}</td>
                    <td class="text-end">{{ $department->subjects_count }}</td>
                    <x-row-actions
                        :record="[
                            'department_id' => $department->id,
                            'dept_name' => $department->dept_name,
                            'course_id' => (string) $department->course_id,
                            'sub_course_id' => (string) $department->sub_course_id,
                        ]"
                        :label="$department->dept_name"
                        edit="edit-department"
                        delete="delete-department"
                        :edit-action="route($portal.'.departments.update', $department)"
                        :delete-action="route($portal.'.departments.destroy', $department)"
                    />
                </tr>
            @empty
                <x-empty-state colspan="6" title="No departments yet" icon="fa-building" />
            @endforelse
        </tbody>
    </x-table-card>

    <x-modal id="add-department" icon="fa-plus" title="Add department" submit="Add department" busy="Adding…" :action="route($portal.'.departments.store')" :show-on-load="$createErrors->any()">
        <div>
            <label class="form-label" for="add_dept_name">Name</label>
            <input id="add_dept_name" name="dept_name" @class(['form-control', 'is-invalid' => $createErrors->has('dept_name')]) required minlength="2" maxlength="100" value="{{ $createErrors->any() ? old('dept_name') : '' }}">
            <x-field-error for="dept_name" bag="createDepartment">Give the department a name.</x-field-error>
        </div>
        <div class="row g-3">
            <div class="col-sm-6">
                <label class="form-label" for="add_course_id">Course</label>
                <select id="add_course_id" name="course_id" @class(['form-select', 'is-invalid' => $createErrors->has('course_id')]) required>
                    <option value="">Choose…</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @selected($createErrors->any() && (string) old('course_id') === (string) $course->id)>{{ $course->name }}</option>
                    @endforeach
                </select>
                <x-field-error for="course_id" bag="createDepartment">Choose the course.</x-field-error>
                @if ($courses->isEmpty())
                    <div class="form-text">No courses yet. <a href="{{ route($portal.'.courses') }}">Add one first</a>.</div>
                @endif
            </div>
            <div class="col-sm-6">
                <label class="form-label" for="add_sub_course_id">Sub-course <span class="text-body-secondary fw-normal">(optional)</span></label>
                <select id="add_sub_course_id" name="sub_course_id" @class(['form-select', 'is-invalid' => $createErrors->has('sub_course_id')]) data-option-filter='{"course":"add_course_id"}'>
                    <option value="" data-label="None" data-empty="No sub-courses for this course">None</option>
                    @foreach ($subCourses as $subCourse)
                        <option value="{{ $subCourse->id }}" data-course="{{ $subCourse->course_id }}" @selected($createErrors->any() && (string) old('sub_course_id') === (string) $subCourse->id)>{{ $subCourse->name }}</option>
                    @endforeach
                </select>
                <x-field-error for="sub_course_id" bag="createDepartment">Choose a sub-course of the selected course, or leave it blank.</x-field-error>
            </div>
        </div>
    </x-modal>

    <x-modal id="edit-department" icon="fa-floppy-disk" title="Edit department" submit="Save changes" :action="$editAction" method="PUT" :show-on-load="$updateErrors->any()">
        <input type="hidden" name="department_id" value="{{ $updateErrors->any() ? old('department_id') : '' }}">
        <div>
            <label class="form-label" for="edit_dept_name">Name</label>
            <input id="edit_dept_name" name="dept_name" @class(['form-control', 'is-invalid' => $updateErrors->has('dept_name')]) required minlength="2" maxlength="100" value="{{ $updateErrors->any() ? old('dept_name') : '' }}">
            <x-field-error for="dept_name" bag="updateDepartment">Give the department a name.</x-field-error>
        </div>
        <div class="row g-3">
            <div class="col-sm-6">
                <label class="form-label" for="edit_course_id">Course</label>
                <select id="edit_course_id" name="course_id" @class(['form-select', 'is-invalid' => $updateErrors->has('course_id')]) required>
                    <option value="">Choose…</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @selected($updateErrors->any() && (string) old('course_id') === (string) $course->id)>{{ $course->name }}</option>
                    @endforeach
                </select>
                <x-field-error for="course_id" bag="updateDepartment">Choose the course.</x-field-error>
                @if ($courses->isEmpty())
                    <div class="form-text">No courses yet. <a href="{{ route($portal.'.courses') }}">Add one first</a>.</div>
                @endif
            </div>
            <div class="col-sm-6">
                <label class="form-label" for="edit_sub_course_id">Sub-course <span class="text-body-secondary fw-normal">(optional)</span></label>
                <select id="edit_sub_course_id" name="sub_course_id" @class(['form-select', 'is-invalid' => $updateErrors->has('sub_course_id')]) data-option-filter='{"course":"edit_course_id"}'>
                    <option value="" data-label="None" data-empty="No sub-courses for this course">None</option>
                    @foreach ($subCourses as $subCourse)
                        <option value="{{ $subCourse->id }}" data-course="{{ $subCourse->course_id }}" @selected($updateErrors->any() && (string) old('sub_course_id') === (string) $subCourse->id)>{{ $subCourse->name }}</option>
                    @endforeach
                </select>
                <x-field-error for="sub_course_id" bag="updateDepartment">Choose a sub-course of the selected course, or leave it blank.</x-field-error>
            </div>
        </div>
    </x-modal>

    <x-modal id="delete-department" title="Delete this department?" submit="Delete" busy="Deleting…" danger :action="route($portal.'.departments')" method="DELETE">
        <p class="mb-0"><strong data-record-text="dept_name"></strong> will be removed. This can't be undone. A department that still has teachers or subjects can't be deleted.</p>
    </x-modal>
</x-layouts.portal>
