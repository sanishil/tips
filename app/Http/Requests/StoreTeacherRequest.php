<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Add a teacher to a department. Errors go to their own bag so the page can reopen the right dialog.
 */
class StoreTeacherRequest extends FormRequest
{
    /**
     * @var string
     */
    protected $errorBag = 'createTeacher';

    /**
     * Who may manage teachers is decided by the route (signed-in admin or tech).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Collapse stray spaces so the same teacher isn't added twice by accident.
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
            'department_id' => ['required', 'integer', Rule::exists('departments', 'id')],
            'name' => [
                'required', 'string', 'min:2', 'max:100',
                "regex:/^[\pL\pM][\pL\pM\s.'-]*$/u",
                Rule::unique('teachers', 'name')
                    ->where('department_id', $this->integer('department_id'))
                    ->ignore($this->route('teacher')),
            ],
            'designation_id' => ['required', 'integer', Rule::exists('designations', 'id')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'department_id.required' => 'Choose the teacher\'s department.',
            'department_id.integer' => 'Choose the teacher\'s department.',
            'department_id.exists' => 'That department no longer exists. Choose another.',
            'name.required' => 'Enter the teacher\'s name.',
            'name.min' => 'That name is too short.',
            'name.max' => 'Keep the name under 100 characters.',
            'name.regex' => 'Use letters, spaces, full stops and hyphens only, e.g. Dr. A. Debnath.',
            'name.unique' => 'This teacher is already listed in that department.',
            'designation_id.required' => 'Choose the teacher\'s designation.',
            'designation_id.integer' => 'Choose the teacher\'s designation.',
            'designation_id.exists' => 'That designation no longer exists. Choose another.',
        ];
    }
}
