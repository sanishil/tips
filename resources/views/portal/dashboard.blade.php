@php
    $totals = [
        'students' => array_sum(array_column($departments, 'students')),
        'teachers' => array_sum(array_column($departments, 'teachers')),
        'subjects' => array_sum(array_column($departments, 'subjects')),
    ];
    $departmentCount = count($departments);
    $figures = [
        'Students' => [$totals['students'], 'in '.$departmentCount.' '.\Illuminate\Support\Str::plural('department', $departmentCount)],
        'Teachers' => [$totals['teachers'], $totals['teachers'] ? '1 for every '.round($totals['students'] / $totals['teachers']).' students' : 'none added yet'],
        'Subjects' => [$totals['subjects'], 'across all courses'],
        'Printed today' => [count($todayPrints), $todayPrints ? 'last at '.$todayPrints[0]['time'] : 'nothing yet'],
    ];
@endphp

<x-layouts.portal :portal="$portal" title="Dashboard">
    <x-page-header title="Dashboard" />

    <section class="card mb-4" aria-label="At a glance">
        <dl class="summary-strip">
            @foreach ($figures as $label => [$value, $note])
                <div>
                    <dt>{{ $label }}</dt>
                    <dd>{{ number_format($value) }} <small>{{ $note }}</small></dd>
                </div>
            @endforeach
        </dl>
    </section>

    <div class="row g-4">
        <div class="col-xl-8">
            <section class="card h-100" aria-labelledby="departments-heading">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h2 id="departments-heading" class="h6 mb-0">Departments</h2>
                    <a href="{{ route($portal.'.departments') }}" class="small">Manage</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr><th>Department</th><th class="text-end">Students</th><th class="text-end">Teachers</th><th class="text-end">Subjects</th></tr>
                        </thead>
                        <tbody class="tw:tabular-nums">
                            @forelse ($departments as $department)
                                <tr>
                                    <td>{{ $department['dept_name'] }}</td>
                                    <td class="text-end">{{ $department['students'] }}</td>
                                    <td class="text-end">{{ $department['teachers'] }}</td>
                                    <td class="text-end">{{ $department['subjects'] }}</td>
                                </tr>
                            @empty
                                <x-empty-state colspan="4" title="No departments yet">
                                    <a href="{{ route($portal.'.departments') }}">Add the first department</a> to start listing subjects and teachers.
                                </x-empty-state>
                            @endforelse
                        </tbody>
                        @if ($departments)
                            <tfoot class="tw:tabular-nums">
                                <tr class="fw-semibold">
                                    <td>Total</td>
                                    <td class="text-end">{{ number_format($totals['students']) }}</td>
                                    <td class="text-end">{{ number_format($totals['teachers']) }}</td>
                                    <td class="text-end">{{ number_format($totals['subjects']) }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </section>
        </div>

        <div class="col-xl-4">
            <section class="card h-100" aria-labelledby="prints-heading">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h2 id="prints-heading" class="h6 mb-0">Printed today</h2>
                    @if (count($todayPrints) > 4)
                        <button type="button" class="btn btn-link btn-sm p-0" data-bs-toggle="modal" data-bs-target="#today-prints"><i class="fa-solid fa-list me-1" aria-hidden="true"></i> See all {{ count($todayPrints) }}</button>
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

    <div class="modal fade" id="today-prints" tabindex="-1" data-bs-backdrop="static" aria-labelledby="today-prints-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5 font-display" id="today-prints-title">Printed today</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr><th>Time</th><th>College ID</th><th>Name</th><th>Subject</th><th>Type</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($todayPrints as $print)
                                <tr>
                                    <td class="tw:tabular-nums">{{ $print['time'] }}</td>
                                    <td class="font-monospace">{{ $print['std_id'] }}</td>
                                    <td>{{ $print['name'] }}</td>
                                    <td>{{ $print['subject'] }}</td>
                                    <td>{{ $print['page_type'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.portal>
