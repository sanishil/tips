<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Course;
use App\Models\Department;
use App\Models\SubCourse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Departments, managed from both portals (/admin/departments and /tech/departments). Each runs under
 * a course and, optionally, one of its sub-courses. The role middleware on those routes guarantees
 * the user's role matches the portal.
 */
class DepartmentController extends Controller
{
    public function index(Request $request): View
    {
        return view('portal.departments', [
            'portal' => $request->user()->role->value,
            'departments' => Department::with(['course', 'subCourse'])->withCount(['teachers', 'subjects'])->orderBy('dept_name')->get(),
            'courses' => Course::orderBy('name')->get(['id', 'name']),
            'subCourses' => SubCourse::orderBy('name')->get(['id', 'course_id', 'name']),
        ]);
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $department = Department::create($request->validated());

        return $this->backToList($request, "“{$department->dept_name}” added.");
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());

        return $this->backToList($request, "“{$department->dept_name}” saved.");
    }

    /**
     * A department that still has teachers or subjects can't go (the database refuses too); say why instead.
     */
    public function destroy(Request $request, Department $department): RedirectResponse
    {
        $teacherCount = $department->teachers()->count();

        if ($teacherCount > 0) {
            return redirect()->route($request->user()->role->value.'.departments')->with(
                'error',
                "“{$department->dept_name}” still has {$teacherCount} ".Str::plural('teacher', $teacherCount).'. Move or remove them first.',
            );
        }

        $subjectCount = $department->subjects()->count();

        if ($subjectCount > 0) {
            return redirect()->route($request->user()->role->value.'.departments')->with(
                'error',
                "“{$department->dept_name}” still has {$subjectCount} ".Str::plural('subject', $subjectCount).'. Move or delete them first.',
            );
        }

        $department->delete();

        return $this->backToList($request, "“{$department->dept_name}” deleted.");
    }

    private function backToList(Request $request, string $message): RedirectResponse
    {
        return redirect()->route($request->user()->role->value.'.departments')->with('status', $message);
    }
}
