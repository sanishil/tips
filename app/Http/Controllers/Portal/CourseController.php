<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use App\Models\SubCourse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Courses and their sub-courses, managed from both portals (/admin/courses and /tech/courses).
 * Sub-courses are listed on the same page; SubCourseController saves them.
 */
class CourseController extends Controller
{
    public function index(Request $request): View
    {
        return view('portal.courses', [
            'portal' => $request->user()->role->value,
            'courses' => Course::withCount('subCourses')->orderBy('name')->get(),
            'subCourses' => SubCourse::with('course')->get()->sortBy(['course.name', 'name'])->values(),
        ]);
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $course = Course::create($request->validated());

        return $this->backToList($request, 'status', "“{$course->name}” added.");
    }

    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $course->update($request->validated());

        return $this->backToList($request, 'status', "“{$course->name}” saved.");
    }

    /**
     * A course that still has sub-courses, departments or subjects can't go (the database refuses too); say why instead.
     */
    public function destroy(Request $request, Course $course): RedirectResponse
    {
        $subCourseCount = $course->subCourses()->count();

        if ($subCourseCount > 0) {
            return $this->backToList(
                $request,
                'error',
                "“{$course->name}” still has {$subCourseCount} ".Str::plural('sub-course', $subCourseCount).'. Move or delete them first.',
            );
        }

        $departmentCount = $course->departments()->count();

        if ($departmentCount > 0) {
            return $this->backToList(
                $request,
                'error',
                "“{$course->name}” is used by {$departmentCount} ".Str::plural('department', $departmentCount).'. Change their course first.',
            );
        }

        $subjectCount = $course->subjects()->count();

        if ($subjectCount > 0) {
            return $this->backToList(
                $request,
                'error',
                "“{$course->name}” still has {$subjectCount} ".Str::plural('subject', $subjectCount).'. Move or delete them first.',
            );
        }

        $course->delete();

        return $this->backToList($request, 'status', "“{$course->name}” deleted.");
    }

    private function backToList(Request $request, string $type, string $message): RedirectResponse
    {
        return redirect()->route($request->user()->role->value.'.courses')->with($type, $message);
    }
}
