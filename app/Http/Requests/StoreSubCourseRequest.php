<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Add a sub-course under a course (e.g. Paramedical under UG). Errors go to their own bag.
 */
class StoreSubCourseRequest extends FormRequest
{
    /**
     * @var string
     */
    protected $errorBag = 'createSubCourse';

    /**
     * Who may manage sub-courses is decided by the route (signed-in admin or tech).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Collapse stray spaces so the same sub-course isn't added twice by accident.
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
            'course_id' => ['required', 'integer', Rule::exists('courses', 'id')],
            'name' => [
                'required', 'string', 'min:2', 'max:60',
                "regex:/^[\pL\pM\pN][\pL\pM\pN\s.&()'-]*$/u",
                Rule::unique('sub_courses', 'name')
                    ->where('course_id', $this->integer('course_id'))
                    ->ignore($this->route('subCourse')),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'course_id.required' => 'Choose the course it belongs to.',
            'course_id.integer' => 'Choose the course it belongs to.',
            'course_id.exists' => 'That course no longer exists. Choose another.',
            'name.required' => 'Enter the sub-course, e.g. Paramedical.',
            'name.min' => 'That name is too short.',
            'name.max' => 'Keep the name under 60 characters.',
            'name.regex' => 'Use letters, numbers and spaces only (& . ( ) - are fine too).',
            'name.unique' => 'This sub-course is already listed under that course.',
        ];
    }
}
