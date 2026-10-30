<?php

namespace App\Http\Requests;

use App\Models\SubCourse;
use App\Models\Subject;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Add a subject to a semester of a course. The sub-course is required when the course has sub-courses and must be
 * left out when it has none; the department must run that exact course and sub-course. Errors go to their own bag
 * so the page can reopen the right dialog.
 */
class StoreSubjectRequest extends FormRequest
{
    /**
     * @var string
     */
    protected $errorBag = 'createSubject';

    /**
     * Who may manage subjects is decided by the route (signed-in admin or tech).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Collapse stray spaces and upper-case the code, so "mlt 302" and "MLT-302" aren't told apart by accident.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => Str::squish((string) $this->input('name')),
            'code' => Str::upper(Str::squish((string) $this->input('code'))),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $courseId = $this->integer('course_id');
        $subCourseId = $this->filled('sub_course_id') ? $this->integer('sub_course_id') : null;
        $sameSubCourse = fn (Builder $query): Builder => $subCourseId === null
            ? $query->whereNull('sub_course_id')
            : $query->where('sub_course_id', $subCourseId);

        return [
            'name' => [
                'required', 'string', 'min:2', 'max:120',
                "regex:/^[\pL\pM\pN][\pL\pM\pN\s.,&()':\/-]*$/u",
                Rule::unique('subjects', 'name')
                    ->where('course_id', $courseId)
                    ->where($sameSubCourse)
                    ->where('semester', $this->integer('semester'))
                    ->ignore($this->route('subject')),
            ],
            'code' => [
                'required', 'string', 'max:20',
                'regex:/^[A-Z]{2,5}-?\d{2,4}$/',
                Rule::unique('subjects', 'code')->ignore($this->route('subject')),
            ],
            'course_id' => ['required', 'integer', Rule::exists('courses', 'id')],
            'sub_course_id' => [
                Rule::requiredIf(fn (): bool => SubCourse::where('course_id', $courseId)->exists()),
                'nullable', 'integer',
                Rule::exists('sub_courses', 'id')->where('course_id', $courseId),
            ],
            'semester' => ['required', 'integer', 'between:1,'.Subject::MAX_SEMESTER],
            'department_id' => [
                'required', 'integer',
                Rule::exists('departments', 'id')->where('course_id', $courseId)->where($sameSubCourse),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Enter the subject name.',
            'name.min' => 'That name is too short.',
            'name.max' => 'Keep the name under 120 characters.',
            'name.regex' => 'Use letters, numbers and spaces only (& . , : ( ) / - are fine too).',
            'name.unique' => 'This subject is already listed for that course, sub-course and semester.',
            'code.required' => 'Enter the subject code, e.g. MLT-302.',
            'code.max' => 'Use a code like MLT-302.',
            'code.regex' => 'Use a code like MLT-302.',
            'code.unique' => 'Another subject already has this code.',
            'course_id.required' => 'Choose the course.',
            'course_id.integer' => 'Choose the course.',
            'course_id.exists' => 'That course no longer exists. Choose another.',
            'sub_course_id.required' => 'Choose the sub-course.',
            'sub_course_id.integer' => 'Choose the sub-course.',
            'sub_course_id.exists' => 'Choose a sub-course of the selected course.',
            'semester.required' => 'Choose the semester.',
            'semester.integer' => 'Choose the semester.',
            'semester.between' => 'Choose a semester from 1 to '.Subject::MAX_SEMESTER.'.',
            'department_id.required' => 'Choose the department.',
            'department_id.integer' => 'Choose the department.',
            'department_id.exists' => 'Choose a department that runs this course and sub-course.',
        ];
    }
}
