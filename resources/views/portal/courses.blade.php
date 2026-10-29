@php
    $createErrors = $errors->getBag('createCourse');
    $updateErrors = $errors->getBag('updateCourse');

    /* After a failed edit, reopen the dialog pointed at the course being edited. */
    $editAction = $updateErrors->any() && old('course_id')
        ? route($portal.'.courses.update', old('course_id'))
        : route($portal.'.courses');

    $dialogs = [
        'add' => ['title' => 'Add course', 'submit' => 'Add course', 'icon' => 'fa-plus', 'errors' => $createErrors, 'bag' => 'createCourse'],
        'edit' => ['title' => 'Edit course', 'submit' => 'Save changes', 'icon' => 'fa-floppy-disk', 'errors' => $updateErrors, 'bag' => 'updateCourse'],
    ];
@endphp

<x-layouts.portal :portal="$portal" title="Courses">
    <x-page-header title="Courses" subtitle="The courses students are enrolled in, and how many semesters each one runs." />

    <x-table-card id="courses-table" title="All courses" :count="$courses->count()" placeholder="Search courses">
        <x-slot:actions>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add-course"><i class="fa-solid fa-plus me-1" aria-hidden="true"></i> Add course</button>
        </x-slot:actions>
        <thead>
            <tr><th>Course</th><th class="text-end">Semesters</th><th>Added</th><th class="text-end" data-export-skip><span class="visually-hidden">Actions</span></th></tr>
        </thead>
        <tbody>
            @forelse ($courses as $course)
                <tr>
                    <td class="fw-semibold">{{ $course->name }}</td>
                    <td class="text-end">{{ $course->semesters }}</td>
                    <td class="text-body-secondary" data-export="{{ $course->created_at->toDateString() }}">{{ $course->created_at->format('j M Y') }}</td>
                    <x-row-actions
                        :record="['course_id' => $course->id, 'name' => $course->name, 'semesters' => (string) $course->semesters]"
                        :label="$course->name"
                        edit="edit-course"
                        delete="delete-course"
                        :edit-action="route($portal.'.courses.update', $course)"
                        :delete-action="route($portal.'.courses.destroy', $course)"
                    />
                </tr>
            @empty
                <x-empty-state colspan="4" title="No courses yet" icon="fa-graduation-cap" />
            @endforelse
        </tbody>
    </x-table-card>

    @foreach ($dialogs as $mode => $dialog)
        @php($bagErrors = $dialog['errors'])
        <x-modal
            :id="$mode.'-course'"
            :icon="$dialog['icon']"
            :title="$dialog['title']"
            :submit="$dialog['submit']"
            :action="$mode === 'add' ? route($portal.'.courses.store') : $editAction"
            :method="$mode === 'add' ? 'POST' : 'PUT'"
            :show-on-load="$bagErrors->any()"
        >
            @if ($mode === 'edit')
                <input type="hidden" name="course_id" value="{{ $bagErrors->any() ? old('course_id') : '' }}">
            @endif
            <div class="row g-3">
                <div class="col-sm-8">
                    <label class="form-label" for="{{ $mode }}_course_name">Course</label>
                    <input id="{{ $mode }}_course_name" name="name" @class(['form-control', 'is-invalid' => $bagErrors->has('name')]) required minlength="2" maxlength="60" placeholder="e.g. B.Sc" value="{{ $bagErrors->any() ? old('name') : '' }}">
                    <x-field-error for="name" :bag="$dialog['bag']">Enter the course name, e.g. B.Sc.</x-field-error>
                </div>
                <div class="col-sm-4">
                    <label class="form-label" for="{{ $mode }}_semesters">Semesters</label>
                    <input id="{{ $mode }}_semesters" name="semesters" type="number" inputmode="numeric" @class(['form-control', 'is-invalid' => $bagErrors->has('semesters')]) required min="1" max="{{ \App\Models\Course::MAX_SEMESTERS }}" step="1" value="{{ $bagErrors->any() ? old('semesters') : '' }}">
                    <x-field-error for="semesters" :bag="$dialog['bag']">Between 1 and {{ \App\Models\Course::MAX_SEMESTERS }}.</x-field-error>
                </div>
            </div>
        </x-modal>
    @endforeach

    <x-modal id="delete-course" title="Delete this course?" submit="Delete" busy="Deleting…" danger :action="route($portal.'.courses')" method="DELETE">
        <p class="mb-0"><strong data-record-text="name"></strong> will be removed. This can't be undone.</p>
    </x-modal>
</x-layouts.portal>
