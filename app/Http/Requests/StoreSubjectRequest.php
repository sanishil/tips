<?php

namespace App\Http\Requests;

use App\Models\Subject;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Add a subject to a department's semester. Errors go to their own bag so the page can reopen the right dialog.
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
        return [
            'name' => [
                'required', 'string', 'min:2', 'max:120',
                "regex:/^[\pL\pM\pN][\pL\pM\pN\s.,&()':\/-]*$/u",
                Rule::unique('subjects', 'name')
                    ->where('department_id', $this->integer('department_id'))
                    ->where('semester', $this->integer('semester'))
                    ->ignore($this->route('subject')),
            ],
            'code' => [
                'required', 'string', 'max:20',
                'regex:/^[A-Z]{2,5}-?\d{2,4}$/',
                Rule::unique('subjects', 'code')->ignore($this->route('subject')),
            ],
            'semester' => ['required', 'integer', 'between:1,'.Subject::MAX_SEMESTER],
            'department_id' => ['required', 'integer', Rule::exists('departments', 'id')],
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
            'name.unique' => 'This subject is already listed for that department and semester.',
            'code.required' => 'Enter the subject code, e.g. MLT-302.',
            'code.max' => 'Use a code like MLT-302.',
            'code.regex' => 'Use a code like MLT-302.',
            'code.unique' => 'Another subject already has this code.',
            'semester.required' => 'Choose the semester.',
            'semester.integer' => 'Choose the semester.',
            'semester.between' => 'Choose a semester from 1 to '.Subject::MAX_SEMESTER.'.',
            'department_id.required' => 'Choose the department.',
            'department_id.integer' => 'Choose the department.',
            'department_id.exists' => 'That department no longer exists. Choose another.',
        ];
    }
}
