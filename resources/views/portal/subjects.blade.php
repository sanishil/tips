<x-layouts.portal :portal="$portal" title="Subjects">
    <x-page-header title="Subjects" subtitle="Students only see the subjects for their own department, course and semester.">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add-subject"><i class="fa-solid fa-plus me-1" aria-hidden="true"></i> Add subject</button>
    </x-page-header>

    <x-table-card id="subjects-table" title="All subjects" :count="count($subjects)" placeholder="Search name or code">
        <x-slot:filters>
            <select class="form-select form-select-sm tw:sm:w-auto" aria-label="Course" data-table-filter="subjects-table" data-filter-key="course">
                <option value="">All courses</option>
                @foreach ($options['courses'] as $course)
                    <option>{{ $course }}</option>
                @endforeach
            </select>
            <select class="form-select form-select-sm tw:sm:w-auto" aria-label="Semester" data-table-filter="subjects-table" data-filter-key="sem">
                <option value="">All semesters</option>
                @foreach ($options['semesters'] as $semester)
                    <option>{{ $semester }}</option>
                @endforeach
            </select>
        </x-slot:filters>

        <thead>
            <tr><th>Code</th><th>Subject</th><th>Semester</th><th>Course</th><th>Department</th><th data-export-skip><span class="visually-hidden">Actions</span></th></tr>
        </thead>
        <tbody>
            @forelse ($subjects as $subject)
                <tr data-course="{{ $subject['course'] }}" data-sem="{{ $subject['sem'] }}">
                    <td class="font-monospace">{{ $subject['code'] }}</td>
                    <td class="fw-semibold">{{ $subject['subjects'] }}</td>
                    <td>{{ $subject['sem'] }}</td>
                    <td>{{ $subject['course'] }}</td>
                    <td>{{ $subject['dept_name'] }}</td>
                    <x-row-actions :record="\Illuminate\Support\Arr::except($subject, 'id')" :label="$subject['subjects']" edit="edit-subject" delete="delete-subject" />
                </tr>
            @empty
                <x-empty-state colspan="6" title="No subjects yet" icon="fa-bookmark">
                    Subjects you add here appear in the student form straight away.
                </x-empty-state>
            @endforelse
        </tbody>
    </x-table-card>

    @foreach (['add' => 'Add subject', 'edit' => 'Edit subject'] as $mode => $heading)
        <x-modal :id="$mode.'-subject'" :icon="$mode === 'add' ? 'fa-plus' : 'fa-floppy-disk'" size="modal-lg" :title="$heading" :submit="$mode === 'add' ? 'Add subject' : 'Save changes'" :message="$mode === 'add' ? 'Subject added.' : 'Changes saved.'">
            <div class="row g-3">
                <div class="col-sm-8">
                    <label class="form-label" for="{{ $mode }}_subjects">Subject name</label>
                    <input id="{{ $mode }}_subjects" name="subjects" class="form-control" required maxlength="120">
                    <x-field-error for="subjects">Enter the subject name.</x-field-error>
                </div>
                <div class="col-sm-4">
                    <label class="form-label" for="{{ $mode }}_code">Code</label>
                    <input id="{{ $mode }}_code" name="code" class="form-control font-monospace" required maxlength="20" pattern="[A-Za-z]{2,5}-?\d{2,4}">
                    <x-field-error for="code">Use a code like MLT-302.</x-field-error>
                </div>
                <div class="col-sm-4">
                    <label class="form-label" for="{{ $mode }}_course">Course</label>
                    <select id="{{ $mode }}_course" name="course" class="form-select" required>
                        @foreach ($options['courses'] as $course)
                            <option>{{ $course }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4">
                    <label class="form-label" for="{{ $mode }}_sem">Semester</label>
                    <select id="{{ $mode }}_sem" name="sem" class="form-select" required>
                        @foreach ($options['semesters'] as $semester)
                            <option>{{ $semester }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4">
                    <label class="form-label" for="{{ $mode }}_dept">Department</label>
                    <select id="{{ $mode }}_dept" name="dept_name" class="form-select" required>
                        @foreach ($departments as $department)
                            <option>{{ $department['dept_name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-modal>
    @endforeach

    <x-modal id="delete-subject" title="Delete this subject?" submit="Delete" busy="Deleting…" danger message="Subject deleted.">
        <p class="mb-0"><strong data-record-text="subjects"></strong> (<span data-record-text="code"></span>) will disappear from the student form. Cover pages already printed aren't affected.</p>
    </x-modal>
</x-layouts.portal>
