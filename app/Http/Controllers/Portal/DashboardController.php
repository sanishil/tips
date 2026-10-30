<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * The dashboard in both portals: counts per department. Student records and printed cover pages
 * aren't stored yet, so those figures stay at zero until they are.
 */
class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $departments = Department::withCount(['teachers', 'subjects'])->orderBy('dept_name')->get()
            ->map(fn (Department $department): array => [
                'dept_name' => $department->dept_name,
                'students' => 0,
                'teachers' => $department->teachers_count,
                'subjects' => $department->subjects_count,
            ])
            ->all();

        return view('portal.dashboard', [
            'portal' => $request->user()->role->value,
            'departments' => $departments,
            'todayPrints' => [],
        ]);
    }
}
