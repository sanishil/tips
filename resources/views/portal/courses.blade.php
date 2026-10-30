@php
    $courseErrors = ['add' => $errors->getBag('createCourse'), 'edit' => $errors->getBag('updateCourse')];
    $subCourseErrors = ['add' => $errors->getBag('createSubCourse'), 'edit' => $errors->getBag('updateSubCourse')];

    /* After a failed edit, reopen the dialog pointed at the row being edited. */
    $courseEditAction = $courseErrors['edit']->any() && old('course_record_id')
        ? route($portal.'.courses.update', old('course_record_id'))
        : route($portal.'.courses');
    $subCourseEditAction = $subCourseErrors['edit']->any() && old('sub_course_id')
        ? route($portal.'.sub-courses.update', old('sub_course_id'))
        : route($portal.'.courses');
@endphp

<x-layouts.portal :portal="$portal" title="Courses">
    <x-page-header title="Courses" />

    {{-- Courses ---------------------------------------------------------------- --}}
    <x-table-card id="courses-table" title="All courses" :count="$courses->count()" placeholder="Search courses">
        <x-slot:actions>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add-course"><i class="fa-solid fa-plus me-1" aria-hidden="true"></i> Add course</button>
        </x-slot:actions>
        <thead>
            <tr><th>Course</th><th class="text-end">Sub-courses</th><th class="text-end" data-export-skip><span class="visually-hidden">Actions</span></th></tr>
        </thead>
        <tbody>
            @forelse ($courses as $course)
                <tr>
                    <td class="fw-semibold">{{ $course->name }}</td>
                    <td class="text-end">{{ $course->sub_courses_count }}</td>
                    <x-row-actions
                        :record="['course_record_id' => $course->id, 'name' => $course->name]"
                        :label="$course->name"
                        edit="edit-course"
                        delete="delete-course"
                        :edit-action="route($portal.'.courses.update', $course)"
                        :delete-action="route($portal.'.courses.destroy', $course)"
                    />
                </tr>
            @empty
                <x-empty-state colspan="3" title="No courses yet" icon="fa-graduation-cap" />
            @endforelse
        </tbody>
    </x-table-card>

    {{-- Sub-courses ------------------------------------------------------------ --}}
    <div class="mt-4">
        <x-table-card id="sub-courses-table" title="Sub-courses" :count="$subCourses->count()" placeholder="Search sub-courses">
            <x-slot:actions>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add-sub-course"><i class="fa-solid fa-plus me-1" aria-hidden="true"></i> Add sub-course</button>
            </x-slot:actions>
            <x-slot:filters>
                <select class="form-select form-select-sm tw:sm:w-auto" aria-label="Course" data-table-filter="sub-courses-table" data-filter-key="course">
                    <option value="">All courses</option>
                    @foreach ($courses as $course)
                        <option>{{ $course->name }}</option>
                    @endforeach
                </select>
            </x-slot:filters>
            <thead>
                <tr><th>Sub-course</th><th>Course</th><th class="text-end" data-export-skip><span class="visually-hidden">Actions</span></th></tr>
            </thead>
            <tbody>
                @forelse ($subCourses as $subCourse)
                    <tr data-course="{{ $subCourse->course->name }}">
                        <td class="fw-semibold">{{ $subCourse->name }}</td>
                        <td><span class="pill pill-info">{{ $subCourse->course->name }}</span></td>
                        <x-row-actions
                            :record="['sub_course_id' => $subCourse->id, 'course_id' => (string) $subCourse->course_id, 'name' => $subCourse->name]"
                            :label="$subCourse->name"
                            edit="edit-sub-course"
                            delete="delete-sub-course"
                            :edit-action="route($portal.'.sub-courses.update', $subCourse)"
                            :delete-action="route($portal.'.sub-courses.destroy', $subCourse)"
                        />
                    </tr>
                @empty
                    <x-empty-state colspan="3" title="No sub-courses yet" icon="fa-diagram-project" />
                @endforelse
            </tbody>
        </x-table-card>
    </div>

    {{-- Course dialogs --}}
    @foreach (['add' => ['Add course', 'Add course', 'fa-plus'], 'edit' => ['Edit course', 'Save changes', 'fa-floppy-disk']] as $mode => [$title, $submit, $icon])
        @php($bagErrors = $courseErrors[$mode])
        <x-modal
            :id="$mode.'-course'"
            :icon="$icon"
            :title="$title"
            :submit="$submit"
            :action="$mode === 'add' ? route($portal.'.courses.store') : $courseEditAction"
            :method="$mode === 'add' ? 'POST' : 'PUT'"
            :show-on-load="$bagErrors->any()"
        >
            @if ($mode === 'edit')
                <input type="hidden" name="course_record_id" value="{{ $bagErrors->any() ? old('course_record_id') : '' }}">
            @endif
            <div>
                <label class="form-label" for="{{ $mode }}_course_name">Course</label>
                <input id="{{ $mode }}_course_name" name="name" @class(['form-control', 'is-invalid' => $bagErrors->has('name')]) required minlength="2" maxlength="60" placeholder="e.g. UG" value="{{ $bagErrors->any() ? old('name') : '' }}">
                <x-field-error for="name" :bag="$mode === 'add' ? 'createCourse' : 'updateCourse'">Enter the course, e.g. UG.</x-field-error>
            </div>
        </x-modal>
    @endforeach

    <x-modal id="delete-course" title="Delete this course?" submit="Delete" busy="Deleting…" danger :action="route($portal.'.courses')" method="DELETE">
        <p class="mb-0"><strong data-record-text="name"></strong> will be removed. A course that still has sub-courses, departments or subjects can't be deleted.</p>
    </x-modal>

    {{-- Sub-course dialogs --}}
    @foreach (['add' => ['Add sub-course', 'Add sub-course', 'fa-plus'], 'edit' => ['Edit sub-course', 'Save changes', 'fa-floppy-disk']] as $mode => [$title, $submit, $icon])
        @php($bagErrors = $subCourseErrors[$mode])
        @php($bag = $mode === 'add' ? 'createSubCourse' : 'updateSubCourse')
        <x-modal
            :id="$mode.'-sub-course'"
            :icon="$icon"
            :title="$title"
            :submit="$submit"
            :action="$mode === 'add' ? route($portal.'.sub-courses.store') : $subCourseEditAction"
            :method="$mode === 'add' ? 'POST' : 'PUT'"
            :show-on-load="$bagErrors->any()"
        >
            @if ($mode === 'edit')
                <input type="hidden" name="sub_course_id" value="{{ $bagErrors->any() ? old('sub_course_id') : '' }}">
            @endif
            <div>
                <label class="form-label" for="{{ $mode }}_sub_course_course">Course</label>
                <select id="{{ $mode }}_sub_course_course" name="course_id" @class(['form-select', 'is-invalid' => $bagErrors->has('course_id')]) required>
                    <option value="">Choose…</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @selected($bagErrors->any() && (string) old('course_id') === (string) $course->id)>{{ $course->name }}</option>
                    @endforeach
                </select>
                <x-field-error for="course_id" :bag="$bag">Choose the course it belongs to.</x-field-error>
                @if ($courses->isEmpty())
                    <div class="form-text">Add a course above first.</div>
                @endif
            </div>
            <div>
                <label class="form-label" for="{{ $mode }}_sub_course_name">Sub-course</label>
                <input id="{{ $mode }}_sub_course_name" name="name" @class(['form-control', 'is-invalid' => $bagErrors->has('name')]) required minlength="2" maxlength="60" placeholder="e.g. Paramedical" value="{{ $bagErrors->any() ? old('name') : '' }}">
                <x-field-error for="name" :bag="$bag">Enter the sub-course, e.g. Paramedical.</x-field-error>
            </div>
        </x-modal>
    @endforeach

    <x-modal id="delete-sub-course" title="Delete this sub-course?" submit="Delete" busy="Deleting…" danger :action="route($portal.'.courses')" method="DELETE">
        <p class="mb-0"><strong data-record-text="name"></strong> will be removed. This can't be undone. A sub-course that still has subjects or departments can't be deleted.</p>
    </x-modal>
</x-layouts.portal>
