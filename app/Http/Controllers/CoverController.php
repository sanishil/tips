<?php

namespace App\Http\Controllers;

use App\Models\AssignmentType;
use App\Models\Course;
use App\Models\Department;
use App\Models\Designation;
use App\Models\SubCourse;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;

/**
 * The cover page form. Departments, courses, sub-courses, subjects, teachers, designations and assignment
 * types come from what the admin and tech portals manage; the form narrows each list by the choices before it.
 * Titles and sections are fixed. Wards have no data source yet, so they stay empty.
 */
class CoverController extends Controller
{
    /**
     * @var list<string>
     */
    public const TITLES = ['Mr.', 'Ms.', 'Mrs.', 'Miss'];

    /**
     * @var list<string>
     */
    public const SECTIONS = ['A', 'B', 'C', 'D'];

    public function __invoke(): View
    {
        return view('cover.form', [
            'departments' => Department::orderBy('dept_name')->get(['id', 'dept_name']),
            'courses' => Course::orderBy('name')->get(['id', 'name']),
            'subCourses' => SubCourse::with('course')->orderBy('name')->get(),
            'semesters' => range(1, Subject::MAX_SEMESTER),
            'subjects' => Subject::with(['department', 'course', 'subCourse'])->orderBy('name')->get(),
            'teachers' => Teacher::with(['department', 'designation'])->orderBy('name')->get(),
            'designations' => Designation::orderBy('name')->pluck('name'),
            'assignmentTypes' => AssignmentType::pluck('name')->sort(SORT_NATURAL | SORT_FLAG_CASE)->values(),
            'options' => ['titles' => self::TITLES, 'sections' => self::SECTIONS],
        ]);
    }
}
