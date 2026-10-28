@php
    $figures = [
        'Students' => array_sum(array_column($departments, 'students')),
        'Teachers' => array_sum(array_column($departments, 'teachers')),
        'Subjects' => array_sum(array_column($departments, 'subjects')),
        'Printed today' => count($todayPrints),
    ];
    $maxStudents = max(array_column($departments, 'students') ?: [1]);
@endphp

<x-layouts.portal :portal="$portal" title="Dashboard">
    <x-page-header title="Dashboard" :subtitle="now()->format('l, j F Y')" />

    <div class="card shadow-sm mb-4">
        <div class="row g-0 figure-strip">
            @foreach ($figures as $label => $value)
                <div class="col-sm-6 col-xl-3 px-4 py-3">
                    <p class="small text-body-secondary mb-1">{{ $label }}</p>
                    <p class="h3 font-display mb-0 tw:tabular-nums">{{ number_format($value) }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <section class="card shadow-sm h-100" aria-labelledby="departments-heading">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h2 id="departments-heading" class="h6 mb-0">Departments</h2>
                    <a href="{{ route($portal.'.departments') }}" class="small">Manage</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-stack align-middle mb-0">
                        <thead>
                            <tr><th>Department</th><th class="tw:w-2/5">Students</th><th class="text-end">Teachers</th><th class="text-end">Subjects</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($departments as $department)
                                <tr>
                                    <td data-label="Department" class="fw-semibold">{{ $department['dept_name'] }}</td>
                                    <td data-label="Students">
                                        <span class="d-flex align-items-center gap-2 w-100 justify-content-end justify-content-md-start">
                                            <span class="tw:tabular-nums tw:w-8">{{ $department['students'] }}</span>
                                            <span class="progress flex-grow-1 d-none d-md-flex" style="height: 6px" aria-hidden="true">
                                                <span class="progress-bar" style="width: {{ round($department['students'] / $maxStudents * 100) }}%"></span>
                                            </span>
                                        </span>
                                    </td>
                                    <td data-label="Teachers" class="text-end">{{ $department['teachers'] }}</td>
                                    <td data-label="Subjects" class="text-end">{{ $department['subjects'] }}</td>
                                </tr>
                            @empty
                                <x-empty-state colspan="4" title="No departments yet">
                                    <a href="{{ route($portal.'.departments') }}">Add the first department</a> to start listing subjects and teachers.
                                </x-empty-state>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-xl-4">
            <section class="card shadow-sm h-100" aria-labelledby="prints-heading">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h2 id="prints-heading" class="h6 mb-0">Printed today</h2>
                    @if (count($todayPrints) > 4)
                        <button type="button" class="btn btn-link btn-sm p-0" data-bs-toggle="modal" data-bs-target="#today-prints">See all {{ count($todayPrints) }}</button>
                    @endif
                </div>
                @if ($todayPrints)
                    <ul class="list-group list-group-flush">
                        @foreach (array_slice($todayPrints, 0, 4) as $print)
                            <li class="list-group-item d-flex gap-3">
                                <span class="text-body-secondary small tw:tabular-nums pt-1">{{ $print['time'] }}</span>
                                <span class="tw:min-w-0">
                                    <span class="d-block text-truncate">{{ $print['name'] }}</span>
                                    <span class="d-block small text-body-secondary text-truncate">{{ $print['page_type'] }} · {{ $print['subject'] }}</span>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <x-empty-state icon="fa-file-lines" title="Nothing printed yet today">
                        Cover pages show up here as students print them.
                    </x-empty-state>
                @endif
            </section>
        </div>
    </div>

    <div class="modal fade" id="today-prints" tabindex="-1" aria-labelledby="today-prints-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5 font-display" id="today-prints-title">Printed today</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <table class="table table-stack align-middle mb-0">
                        <thead>
                            <tr><th>Time</th><th>College ID</th><th>Name</th><th>Subject</th><th>Type</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($todayPrints as $print)
                                <tr>
                                    <td data-label="Time" class="tw:tabular-nums">{{ $print['time'] }}</td>
                                    <td data-label="College ID" class="font-monospace">{{ $print['std_id'] }}</td>
                                    <td data-label="Name">{{ $print['name'] }}</td>
                                    <td data-label="Subject">{{ $print['subject'] }}</td>
                                    <td data-label="Type">{{ $print['page_type'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.portal>
