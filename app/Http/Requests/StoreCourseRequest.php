<?php

namespace App\Http\Requests;

use App\Models\Course;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Add a course. Errors go to their own bag so the page can reopen the right dialog.
 */
class StoreCourseRequest extends FormRequest
{
    /**
     * @var string
     */
    protected $errorBag = 'createCourse';

    /**
     * Who may manage courses is decided by the route (signed-in admin or tech).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Collapse stray spaces so "B.Sc " and "B.Sc" count as the same course.
     */
    protected function prepareForValidation(): void
    {
        $this->merge(['name' => Str::squish((string) $this->input('name'))]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'min:2', 'max:60',
                "regex:/^[\pL\pM\pN][\pL\pM\pN\s.&()'-]*$/u",
                Rule::unique('courses', 'name')->ignore($this->route('course')),
            ],
            'semesters' => ['required', 'integer', 'between:1,'.Course::MAX_SEMESTERS],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Enter the course name, e.g. B.Sc.',
            'name.min' => 'That name is too short.',
            'name.max' => 'Keep the name under 60 characters.',
            'name.regex' => 'Use letters, numbers and spaces only (& . ( ) - are fine too).',
            'name.unique' => 'This course is already listed.',
            'semesters.required' => 'How many semesters does it run?',
            'semesters.integer' => 'Enter a whole number of semesters.',
            'semesters.between' => 'A course runs between 1 and '.Course::MAX_SEMESTERS.' semesters.',
        ];
    }
}
