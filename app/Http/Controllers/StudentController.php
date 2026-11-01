<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveStudentRequest;
use App\Models\Student;
use Illuminate\Http\JsonResponse;

/**
 * Student records behind the cover page form: looked up by university roll number to fill the form in, and saved
 * when the student prints (updated if the roll number is already on file, created if not).
 */
class StudentController extends Controller
{
    public function show(string $tuRoll): JsonResponse
    {
        $student = Student::with('department')->where('tu_roll', $tuRoll)->firstOrFail();

        return response()->json($student->toCoverForm());
    }

    public function store(SaveStudentRequest $request): JsonResponse
    {
        $student = Student::updateOrCreate(
            ['tu_roll' => $request->validated('tu_roll')],
            $request->studentAttributes(),
        );

        return response()->json(['saved' => true], $student->wasRecentlyCreated ? 201 : 200);
    }
}
