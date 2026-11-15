<?php

namespace App\Http\Requests;

use App\Http\Controllers\CoverController;
use App\Models\Course;
use App\Models\Department;
use App\Models\SubCourse;
use App\Models\Subject;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * The student part of the cover page form (college ID down to section). The form sends names, not IDs, so the
 * department is checked against the chosen course and sub-course by name. Other fields on the form are ignored.
 */
class SaveStudentRequest extends FormRequest
{
    /**
     * Anyone who can open the cover page form may save their details.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Collapse stray spaces, so "Rina  Das" is saved as "Rina Das".
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'std_id' => Str::squish((string) $this->input('std_id')),
            'tu_roll' => Str::squish((string) $this->input('tu_roll')) ?: null,
            'name' => Str::squish((string) $this->input('name')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $course = Course::firstWhere('name', (string) $this->input('course'));
        $subCourse = $course?->subCourses()->firstWhere('name', (string) $this->input('sub_course'));

        return [
            'std_id' => ['required', 'string', 'regex:/^\d{5,15}$/'],
            'tu_roll' => ['nullable', 'string', 'max:30'],
            'title' => ['required', Rule::in(CoverController::TITLES)],
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'course' => ['required', 'string', Rule::exists('courses', 'name')],
            'sub_course' => [
                Rule::requiredIf(fn (): bool => $course !== null && SubCourse::where('course_id', $course->id)->exists()),
                'nullable', 'string',
                Rule::exists('sub_courses', 'name')->where('course_id', $course?->id),
            ],
            'std_dept' => [
                'required', 'string',
                Rule::exists('departments', 'dept_name')
                    ->where('course_id', $course?->id)
                    ->where(fn (Builder $query): Builder => $subCourse === null
                        ? $query->whereNull('sub_course_id')
                        : $query->where('sub_course_id', $subCourse->id)),
            ],
            'sem' => ['required', 'integer', 'between:1,'.Subject::MAX_SEMESTER],
            'section' => ['nullable', Rule::in(CoverController::SECTIONS)],
        ];
    }

    /**
     * The validated fields as Student attributes.
     *
     * @return array{tu_roll: ?string, title: string, name: string, department_id: int, semester: int, section: ?string}
     */
    public function studentAttributes(): array
    {
        return [
            'tu_roll' => $this->validated('tu_roll'),
            'title' => $this->validated('title'),
            'name' => $this->validated('name'),
            'department_id' => Department::where('dept_name', $this->validated('std_dept'))->value('id'),
            'semester' => (int) $this->validated('sem'),
            'section' => $this->validated('section'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'std_id.required' => 'Enter your college ID (numbers only).',
            'std_id.regex' => 'Enter your college ID (5 to 15 digits).',
            'tu_roll.max' => 'Keep the roll number under 30 characters.',
            'title.required' => 'Choose a title.',
            'title.in' => 'Choose a title.',
            'name.required' => 'Enter your name as it appears on your ID card.',
            'name.min' => 'That name is too short.',
            'name.max' => 'Keep the name under 100 characters.',
            'course.required' => 'Choose your course.',
            'course.exists' => 'That course no longer exists. Choose another.',
            'sub_course.required' => 'Choose your sub-course.',
            'sub_course.exists' => 'Choose a sub-course of the selected course.',
            'std_dept.required' => 'Choose your department.',
            'std_dept.exists' => 'Choose a department that runs this course and sub-course.',
            'sem.required' => 'Choose a semester.',
            'sem.integer' => 'Choose a semester.',
            'sem.between' => 'Choose a semester from 1 to '.Subject::MAX_SEMESTER.'.',
            'section.in' => 'Choose a section from the list.',
        ];
    }
}
