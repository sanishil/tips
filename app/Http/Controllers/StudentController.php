<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveStudentRequest;
use App\Models\Student;
use Illuminate\Http\JsonResponse;

/**
 * Student records behind the cover page form: looked up by college ID to fill the form in, and saved when the
 * student prints (updated if the ID is already on file, created if not).
 */
class StudentController extends Controller
{
    public function show(string $stdId): JsonResponse
    {
        $student = Student::with(['department.course', 'department.subCourse'])->where('std_id', $stdId)->firstOrFail();

        return response()->json($student->toCoverForm());
    }

    public function store(SaveStudentRequest $request): JsonResponse
    {
        $student = Student::updateOrCreate(
            ['std_id' => $request->validated('std_id')],
            $request->studentAttributes(),
        );

        return response()->json(['saved' => true], $student->wasRecentlyCreated ? 201 : 200);
    }
}
