<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubCourseRequest;
use App\Http\Requests\UpdateSubCourseRequest;
use App\Models\SubCourse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Sub-courses (e.g. UG → Paramedical). They're listed on the Courses page, so every action returns there.
 */
class SubCourseController extends Controller
{
    public function store(StoreSubCourseRequest $request): RedirectResponse
    {
        $subCourse = SubCourse::create($request->validated());

        return $this->backToCourses($request, "“{$subCourse->name}” added under {$subCourse->course->name}.");
    }

    public function update(UpdateSubCourseRequest $request, SubCourse $subCourse): RedirectResponse
    {
        $subCourse->update($request->validated());

        return $this->backToCourses($request, "“{$subCourse->name}” saved.");
    }

    public function destroy(Request $request, SubCourse $subCourse): RedirectResponse
    {
        $subCourse->delete();

        return $this->backToCourses($request, "“{$subCourse->name}” deleted.");
    }

    private function backToCourses(Request $request, string $message): RedirectResponse
    {
        return redirect()->route($request->user()->role->value.'.courses')->with('status', $message);
    }
}
