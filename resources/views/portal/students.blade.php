<x-layouts.portal :portal="$portal" title="Students">
    <x-page-header title="Students" subtitle="A record is created the first time a student prints a cover page.">
        @if ($students)
            <button type="button" class="btn btn-outline-secondary" data-export-table="students-table" data-export-name="students">
                <i class="fa-solid fa-file-csv me-1" aria-hidden="true"></i> Export CSV
            </button>
        @endif
    </x-page-header>

    <x-table-card id="students-table" title="Student records" :count="count($students)" placeholder="Search ID, name or subject">
        <x-slot:filters>
            <select class="form-select form-select-sm tw:sm:w-auto" aria-label="Department" data-table-filter="students-table" data-filter-key="dept">
                <option value="">All departments</option>
                @foreach ($departments as $department)
                    <option>{{ $department['dept_name'] }}</option>
                @endforeach
            </select>
        </x-slot:filters>

        <thead>
            <tr><th>Student</th><th>College ID</th><th>Department</th><th>Course</th><th>Last subject</th><th>Teacher</th><th class="text-end">Reprints</th><th>Submitted</th><th data-export-skip><span class="visually-hidden">Actions</span></th></tr>
        </thead>
        <tbody>
            @forelse ($students as $student)
                <tr data-dept="{{ $student['std_dept'] }}">
                    <td class="fw-semibold">{{ $student['name'] }}</td>
                    <td class="font-monospace">{{ $student['std_id'] }}</td>
                    <td>{{ $student['std_dept'] }}</td>
                    <td>{{ $student['course'] }}, {{ $student['sem'] }} sem, sec {{ $student['section'] }}</td>
                    <td>{{ $student['subjects'] }} <span class="text-body-secondary">({{ $student['code'] }})</span></td>
                    <td>{{ $student['f_name'] }}</td>
                    <td class="text-end">{{ $student['reprint'] }}</td>
                    <td data-export="{{ $student['dos'] }}">{{ \Illuminate\Support\Carbon::parse($student['dos'])->format('j M Y') }}</td>
                    <x-row-actions :record="['name' => $student['name'], 'std_id' => $student['std_id']]" :label="$student['name']" delete="delete-student" />
                </tr>
            @empty
                <x-empty-state colspan="9" title="No students yet" icon="fa-address-card">
                    Records appear here once students start printing cover pages.
                    <a href="{{ route('cover.form') }}">Open the cover page form</a>
                </x-empty-state>
            @endforelse
        </tbody>
    </x-table-card>

    <x-modal id="delete-student" title="Delete this student record?" submit="Delete" busy="Deleting…" danger message="Student record deleted.">
        <p class="mb-0">The record for <strong data-record-text="name"></strong> (<span data-record-text="std_id"></span>) and its print history will be deleted. If they print again, a new record is created.</p>
    </x-modal>
</x-layouts.portal>
