@php
    $createErrors = $errors->getBag('createSubject');
    $updateErrors = $errors->getBag('updateSubject');

    /* After a failed edit, reopen the dialog pointed at the subject being edited. */
    $editAction = $updateErrors->any() && old('subject_id')
        ? route($portal.'.subjects.update', old('subject_id'))
        : route($portal.'.subjects');

    $dialogs = [
        'add' => ['title' => 'Add subject', 'submit' => 'Add subject', 'icon' => 'fa-plus', 'errors' => $createErrors, 'bag' => 'createSubject'],
        'edit' => ['title' => 'Edit subject', 'submit' => 'Save changes', 'icon' => 'fa-floppy-disk', 'errors' => $updateErrors, 'bag' => 'updateSubject'],
    ];

    $semesters = range(1, \App\Models\Subject::MAX_SEMESTER);
@endphp

<x-layouts.portal :portal="$portal" title="Subjects">
    <x-page-header title="Subjects" />

    <x-table-card id="subjects-table" title="All subjects" :count="$subjects->count()" placeholder="Search name or code">
        <x-slot:actions>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add-subject"><i class="fa-solid fa-plus me-1" aria-hidden="true"></i> Add subject</button>
        </x-slot:actions>
        <x-slot:filters>
            <select class="form-select form-select-sm tw:sm:w-auto" aria-label="Course" data-table-filter="subjects-table" data-filter-key="course">
                <option value="">All courses</option>
                @foreach ($courses as $course)
                    <option>{{ $course->name }}</option>
                @endforeach
            </select>
            <select class="form-select form-select-sm tw:sm:w-auto" aria-label="Semester" data-table-filter="subjects-table" data-filter-key="sem">
                <option value="">All semesters</option>
                @foreach ($semesters as $semester)
                    <option value="{{ $semester }}">Semester {{ $semester }}</option>
                @endforeach
            </select>
        </x-slot:filters>

        <thead>
            <tr><th>Code</th><th>Subject</th><th>Semester</th><th>Course</th><th>Sub-course</th><th>Department</th><th data-export-skip><span class="visually-hidden">Actions</span></th></tr>
        </thead>
        <tbody>
            @forelse ($subjects as $subject)
                <tr data-course="{{ $subject->course->name }}" data-sem="{{ $subject->semester }}">
                    <td class="font-monospace">{{ $subject->code }}</td>
                    <td class="fw-semibold">{{ $subject->name }}</td>
                    <td>{{ $subject->semester }}</td>
                    <td>{{ $subject->course->name }}</td>
                    <td>{{ $subject->subCourse?->name ?? '—' }}</td>
                    <td>{{ $subject->department->dept_name }}</td>
                    <x-row-actions
                        :record="[
                            'subject_id' => $subject->id,
                            'name' => $subject->name,
                            'code' => $subject->code,
                            'course_id' => (string) $subject->course_id,
                            'sub_course_id' => (string) $subject->sub_course_id,
                            'semester' => (string) $subject->semester,
                            'department_id' => (string) $subject->department_id,
                        ]"
                        :label="$subject->name"
                        edit="edit-subject"
                        delete="delete-subject"
                        :edit-action="route($portal.'.subjects.update', $subject)"
                        :delete-action="route($portal.'.subjects.destroy', $subject)"
                    />
                </tr>
            @empty
                <x-empty-state colspan="7" title="No subjects yet" icon="fa-bookmark" />
            @endforelse
        </tbody>
    </x-table-card>

    @foreach ($dialogs as $mode => $dialog)
        @php($bagErrors = $dialog['errors'])
        <x-modal
            :id="$mode.'-subject'"
            :icon="$dialog['icon']"
            size="modal-lg"
            :title="$dialog['title']"
            :submit="$dialog['submit']"
            :action="$mode === 'add' ? route($portal.'.subjects.store') : $editAction"
            :method="$mode === 'add' ? 'POST' : 'PUT'"
            :show-on-load="$bagErrors->any()"
        >
            @if ($mode === 'edit')
                <input type="hidden" name="subject_id" value="{{ $bagErrors->any() ? old('subject_id') : '' }}">
            @endif
            <div class="row g-3">
                <div class="col-sm-8">
                    <label class="form-label" for="{{ $mode }}_subject_name">Subject name</label>
                    <input id="{{ $mode }}_subject_name" name="name" @class(['form-control', 'is-invalid' => $bagErrors->has('name')]) required minlength="2" maxlength="120" value="{{ $bagErrors->any() ? old('name') : '' }}">
                    <x-field-error for="name" :bag="$dialog['bag']">Enter the subject name.</x-field-error>
                </div>
                <div class="col-sm-4">
                    <label class="form-label" for="{{ $mode }}_subject_code">Code</label>
                    <input id="{{ $mode }}_subject_code" name="code" @class(['form-control font-monospace', 'is-invalid' => $bagErrors->has('code')]) required maxlength="20" pattern="[A-Za-z]{2,5}-?\d{2,4}" placeholder="e.g. MLT-302" value="{{ $bagErrors->any() ? old('code') : '' }}">
                    <x-field-error for="code" :bag="$dialog['bag']">Use a code like MLT-302.</x-field-error>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="{{ $mode }}_subject_course">Course</label>
                    <select id="{{ $mode }}_subject_course" name="course_id" @class(['form-select', 'is-invalid' => $bagErrors->has('course_id')]) required>
                        <option value="">Choose…</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" @selected($bagErrors->any() && (string) old('course_id') === (string) $course->id)>{{ $course->name }}</option>
                        @endforeach
                    </select>
                    <x-field-error for="course_id" :bag="$dialog['bag']">Choose the course.</x-field-error>
                    @if ($courses->isEmpty())
                        <div class="form-text">No courses yet. <a href="{{ route($portal.'.courses') }}">Add one first</a>.</div>
                    @endif
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="{{ $mode }}_subject_sub_course">Sub-course</label>
                    <select id="{{ $mode }}_subject_sub_course" name="sub_course_id" @class(['form-select', 'is-invalid' => $bagErrors->has('sub_course_id')]) required
                            data-option-filter='{"course":"{{ $mode }}_subject_course"}' data-option-filter-exact data-option-empty-disables>
                        <option value="" data-label="Choose…" data-empty="No sub-courses for this course" data-waiting="Choose a course first">Choose…</option>
                        @foreach ($subCourses as $subCourse)
                            <option value="{{ $subCourse->id }}" data-course="{{ $subCourse->course_id }}" @selected($bagErrors->any() && (string) old('sub_course_id') === (string) $subCourse->id)>{{ $subCourse->name }}</option>
                        @endforeach
                    </select>
                    <x-field-error for="sub_course_id" :bag="$dialog['bag']">Choose the sub-course.</x-field-error>
                </div>
                <div class="col-sm-4">
                    <label class="form-label" for="{{ $mode }}_subject_semester">Semester</label>
                    <select id="{{ $mode }}_subject_semester" name="semester" @class(['form-select', 'is-invalid' => $bagErrors->has('semester')]) required>
                        <option value="">Choose…</option>
                        @foreach ($semesters as $semester)
                            <option value="{{ $semester }}" @selected($bagErrors->any() && (string) old('semester') === (string) $semester)>{{ $semester }}</option>
                        @endforeach
                    </select>
                    <x-field-error for="semester" :bag="$dialog['bag']">Choose the semester.</x-field-error>
                </div>
                <div class="col-sm-8">
                    <label class="form-label" for="{{ $mode }}_subject_dept">Department</label>
                    <select id="{{ $mode }}_subject_dept" name="department_id" @class(['form-select', 'is-invalid' => $bagErrors->has('department_id')]) required
                            data-option-filter='{"course":"{{ $mode }}_subject_course","subCourse":"{{ $mode }}_subject_sub_course"}' data-option-filter-exact>
                        <option value="" data-label="Choose…" data-empty="No departments for this course and sub-course" data-waiting="Choose a course first">Choose…</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" data-course="{{ $department->course_id }}" data-sub-course="{{ $department->sub_course_id }}" @selected($bagErrors->any() && (string) old('department_id') === (string) $department->id)>{{ $department->dept_name }}</option>
                        @endforeach
                    </select>
                    <x-field-error for="department_id" :bag="$dialog['bag']">Choose a department that runs this course and sub-course.</x-field-error>
                    @if ($departments->isEmpty())
                        <div class="form-text">No departments with a course yet. <a href="{{ route($portal.'.departments') }}">Set one up first</a>.</div>
                    @endif
                </div>
            </div>
        </x-modal>
    @endforeach

    <x-modal id="delete-subject" title="Delete this subject?" submit="Delete" busy="Deleting…" danger :action="route($portal.'.subjects')" method="DELETE">
        <p class="mb-0"><strong data-record-text="name"></strong> (<span data-record-text="code"></span>) will disappear from the student form. Cover pages already printed aren't affected.</p>
    </x-modal>
</x-layouts.portal>
