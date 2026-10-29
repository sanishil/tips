<x-layouts.portal :portal="$portal" title="Teachers">
    <x-page-header title="Teachers" subtitle="Students pick from this list under “Submitted to”." />

    <x-table-card id="teachers-table" title="All teachers" :count="count($teachers)" placeholder="Search teachers">
        <x-slot:actions>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add-teacher"><i class="fa-solid fa-user-plus me-1" aria-hidden="true"></i> Add teacher</button>
        </x-slot:actions>
        <x-slot:filters>
            <select class="form-select form-select-sm tw:sm:w-auto" aria-label="Department" data-table-filter="teachers-table" data-filter-key="dept">
                <option value="">All departments</option>
                @foreach ($departments as $department)
                    <option>{{ $department['dept_name'] }}</option>
                @endforeach
            </select>
        </x-slot:filters>

        <thead>
            <tr><th>Name</th><th>Designation</th><th>Department</th><th data-export-skip><span class="visually-hidden">Actions</span></th></tr>
        </thead>
        <tbody>
            @forelse ($teachers as $teacher)
                <tr data-dept="{{ $teacher['dept_name'] }}">
                    <td class="fw-semibold">{{ $teacher['teacher'] }}</td>
                    <td>{{ $teacher['designation'] }}</td>
                    <td>{{ $teacher['dept_name'] }}</td>
                    <x-row-actions :record="\Illuminate\Support\Arr::except($teacher, 'id')" :label="$teacher['teacher']" edit="edit-teacher" delete="delete-teacher" />
                </tr>
            @empty
                <x-empty-state colspan="4" title="No teachers yet" icon="fa-user">
                    Add teachers so students can choose who they're submitting to.
                </x-empty-state>
            @endforelse
        </tbody>
    </x-table-card>

    @foreach (['add' => 'Add teacher', 'edit' => 'Edit teacher'] as $mode => $heading)
        <x-modal :id="$mode.'-teacher'" :icon="$mode === 'add' ? 'fa-plus' : 'fa-floppy-disk'" :title="$heading" :submit="$mode === 'add' ? 'Add teacher' : 'Save changes'" :message="$mode === 'add' ? 'Teacher added.' : 'Changes saved.'">
            <div>
                <label class="form-label" for="{{ $mode }}_teacher">Full name</label>
                <input id="{{ $mode }}_teacher" name="teacher" class="form-control" required maxlength="100">
                <x-field-error for="teacher">Enter the teacher's name, including Dr. if they use it.</x-field-error>
            </div>
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label" for="{{ $mode }}_designation">Designation</label>
                    <select id="{{ $mode }}_designation" name="designation" class="form-select" required>
                        @foreach ($options['designations'] ?? [] as $designation)
                            <option>{{ $designation }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="{{ $mode }}_teacher_dept">Department</label>
                    <select id="{{ $mode }}_teacher_dept" name="dept_name" class="form-select" required>
                        @foreach ($departments as $department)
                            <option>{{ $department['dept_name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-modal>
    @endforeach

    <x-modal id="delete-teacher" icon="fa-user-minus" title="Remove this teacher?" submit="Remove" busy="Removing…" danger message="Teacher removed.">
        <p class="mb-0"><strong data-record-text="teacher"></strong> will no longer appear in the student form. Cover pages already printed aren't affected.</p>
    </x-modal>
</x-layouts.portal>
