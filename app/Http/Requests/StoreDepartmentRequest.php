<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Add a department. Errors go to their own bag so the page can reopen the right dialog.
 */
class StoreDepartmentRequest extends FormRequest
{
    /**
     * @var string
     */
    protected $errorBag = 'createDepartment';

    /**
     * Who may manage departments is decided by the route (signed-in admin or tech).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Collapse stray spaces so "Optometry " and "Optometry" count as the same name.
     */
    protected function prepareForValidation(): void
    {
        $this->merge(['dept_name' => Str::squish((string) $this->input('dept_name'))]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dept_name' => [
                'required', 'string', 'min:2', 'max:100',
                "regex:/^[\pL\pM][\pL\pM\s&.,()\/'-]*$/u",
                Rule::unique('departments', 'dept_name')->ignore($this->route('department')),
            ],
            'course_id' => ['required', 'integer', Rule::exists('courses', 'id')],
            'sub_course_id' => [
                'nullable', 'integer',
                Rule::exists('sub_courses', 'id')->where('course_id', $this->integer('course_id')),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'dept_name.required' => 'Give the department a name.',
            'dept_name.min' => 'That name is too short.',
            'dept_name.max' => 'Keep the name under 100 characters.',
            'dept_name.regex' => 'Use letters and spaces only (& . , ( ) / - are fine too).',
            'dept_name.unique' => 'There is already a department with this name.',
            'course_id.required' => 'Choose the course.',
            'course_id.integer' => 'Choose the course.',
            'course_id.exists' => 'That course no longer exists. Choose another.',
            'sub_course_id.integer' => 'Choose a sub-course, or leave it blank.',
            'sub_course_id.exists' => 'Choose a sub-course of the selected course, or leave it blank.',
        ];
    }
}
