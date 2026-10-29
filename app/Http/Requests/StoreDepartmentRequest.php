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
        $this->merge([
            'dept_name' => Str::squish((string) $this->input('dept_name')),
            'hod' => Str::squish((string) $this->input('hod')),
        ]);
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
                "regex:/^[\pL\pM][\pL\pM\s&.,()'-]*$/u",
                Rule::unique('departments', 'dept_name')->ignore($this->route('department')),
            ],
            'hod' => ['required', 'string', 'min:2', 'max:100', "regex:/^[\pL\pM][\pL\pM\s.'-]*$/u"],
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
            'dept_name.regex' => 'Use letters and spaces only (& . , ( ) - are fine too).',
            'dept_name.unique' => 'There is already a department with this name.',
            'hod.required' => 'Who heads this department?',
            'hod.min' => 'That name is too short.',
            'hod.max' => 'Keep the name under 100 characters.',
            'hod.regex' => 'Use letters, spaces, full stops and hyphens only, e.g. Dr. A. Debnath.',
        ];
    }
}
