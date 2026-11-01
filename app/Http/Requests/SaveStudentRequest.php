<?php

namespace App\Http\Requests;

use App\Http\Controllers\CoverController;
use App\Models\Department;
use App\Models\Subject;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * The student part of the cover page form (university roll number down to section). The form sends the department
 * by name, not ID. Other fields on the form are ignored.
 */
class SaveStudentRequest extends FormRequest
{
    /**
     * The shape of a university roll number, shared with the form and the lookup route.
     */
    public const ROLL_PATTERN = '[A-Za-z0-9][A-Za-z0-9-]{2,29}';

    /**
     * Anyone who can open the cover page form may save their details.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Collapse stray spaces and upper-case the roll number, so "Rina  Das" is saved as "Rina Das" and "gdp-21" as
     * "GDP-21".
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'tu_roll' => Str::upper(Str::squish((string) $this->input('tu_roll'))),
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
        return [
            'tu_roll' => ['required', 'string', 'regex:/^'.self::ROLL_PATTERN.'$/'],
            'title' => ['required', Rule::in(CoverController::TITLES)],
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'std_dept' => ['required', 'string', Rule::exists('departments', 'dept_name')],
            'sem' => ['required', 'integer', 'between:1,'.Subject::MAX_SEMESTER],
            'section' => ['nullable', Rule::in(CoverController::SECTIONS)],
        ];
    }

    /**
     * The validated fields as Student attributes.
     *
     * @return array{title: string, name: string, department_id: int, semester: int, section: ?string}
     */
    public function studentAttributes(): array
    {
        return [
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
            'tu_roll.required' => 'Enter your university roll number.',
            'tu_roll.regex' => 'Enter your university roll number (3 to 30 letters, numbers or hyphens).',
            'title.required' => 'Choose a title.',
            'title.in' => 'Choose a title.',
            'name.required' => 'Enter your name as it appears on your ID card.',
            'name.min' => 'That name is too short.',
            'name.max' => 'Keep the name under 100 characters.',
            'std_dept.required' => 'Choose your department.',
            'std_dept.exists' => 'That department no longer exists. Choose another.',
            'sem.required' => 'Choose a semester.',
            'sem.integer' => 'Choose a semester.',
            'sem.between' => 'Choose a semester from 1 to '.Subject::MAX_SEMESTER.'.',
            'section.in' => 'Choose a section from the list.',
        ];
    }
}
