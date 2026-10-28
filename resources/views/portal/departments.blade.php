<x-layouts.portal :portal="$portal" title="Departments">
    <x-page-header title="Departments" subtitle="Subjects, teachers and students all belong to a department.">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-department">Add department</button>
    </x-page-header>

    <x-table-card id="departments-table" title="All departments" :count="count($departments)" placeholder="Search departments">
        <thead>
            <tr><th>Department</th><th>Head of department</th><th class="text-end">Students</th><th class="text-end">Teachers</th><th class="text-end">Subjects</th><th class="text-end" data-export-skip><span class="visually-hidden">Actions</span></th></tr>
        </thead>
        <tbody>
            @forelse ($departments as $department)
                <tr>
                    <td data-label="Department" class="fw-semibold">{{ $department['dept_name'] }}</td>
                    <td data-label="Head">{{ $department['hod'] }}</td>
                    <td data-label="Students" class="text-end">{{ $department['students'] }}</td>
                    <td data-label="Teachers" class="text-end">{{ $department['teachers'] }}</td>
                    <td data-label="Subjects" class="text-end">{{ $department['subjects'] }}</td>
                    <x-row-actions :record="['dept_name' => $department['dept_name'], 'hod' => $department['hod']]" :label="$department['dept_name']" edit="edit-department" delete="delete-department" />
                </tr>
            @empty
                <x-empty-state colspan="6" title="No departments yet" icon="fa-building">
                    Add your first department — you'll need one before adding subjects or teachers.
                </x-empty-state>
            @endforelse
        </tbody>
    </x-table-card>

    @foreach (['add' => 'Add department', 'edit' => 'Edit department'] as $mode => $heading)
        <x-modal :id="$mode.'-department'" :title="$heading" :submit="$mode === 'add' ? 'Add department' : 'Save changes'" :message="$mode === 'add' ? 'Department added.' : 'Changes saved.'">
            <div>
                <label class="form-label" for="{{ $mode }}_dept_name">Name</label>
                <input id="{{ $mode }}_dept_name" name="dept_name" class="form-control" required maxlength="100">
                <x-field-error for="dept_name">Give the department a name.</x-field-error>
            </div>
            <div>
                <label class="form-label" for="{{ $mode }}_hod">Head of department</label>
                <input id="{{ $mode }}_hod" name="hod" class="form-control" required maxlength="100">
                <x-field-error for="hod">Who heads this department?</x-field-error>
            </div>
        </x-modal>
    @endforeach

    <x-modal id="delete-department" title="Delete this department?" submit="Delete" busy="Deleting…" danger message="Department deleted.">
        <p class="mb-0"><strong data-record-text="dept_name"></strong> will be removed. Move or delete its subjects and teachers first.</p>
    </x-modal>
</x-layouts.portal>
