<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Courses, managed from both portals (/admin/courses and /tech/courses).
 */
class CourseController extends Controller
{
    public function index(Request $request): View
    {
        return view('portal.courses', [
            'portal' => $request->user()->role->value,
            'courses' => Course::orderBy('name')->get(),
        ]);
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $course = Course::create($request->validated());

        return $this->backToList($request, "“{$course->name}” added.");
    }

    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $course->update($request->validated());

        return $this->backToList($request, "“{$course->name}” saved.");
    }

    public function destroy(Request $request, Course $course): RedirectResponse
    {
        $course->delete();

        return $this->backToList($request, "“{$course->name}” deleted.");
    }

    private function backToList(Request $request, string $message): RedirectResponse
    {
        return redirect()->route($request->user()->role->value.'.courses')->with('status', $message);
    }
}
