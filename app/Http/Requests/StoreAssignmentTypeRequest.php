<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Add an assignment type. Errors go to their own bag so the page can reopen the right dialog.
 */
class StoreAssignmentTypeRequest extends FormRequest
{
    /**
     * @var string
     */
    protected $errorBag = 'createAssignmentType';

    /**
     * Who may manage assignment types is decided by the route (signed-in admin or tech).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Collapse stray spaces so "Assignment  1" and "Assignment 1" count as the same.
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
                Rule::unique('assignment_types', 'name')->ignore($this->route('assignmentType')),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Enter an assignment type, e.g. Assignment 1.',
            'name.min' => 'That assignment type is too short.',
            'name.max' => 'Keep the assignment type under 60 characters.',
            'name.regex' => 'Use letters, numbers and spaces only (& . ( ) - are fine too).',
            'name.unique' => 'This assignment type is already listed.',
        ];
    }
}
