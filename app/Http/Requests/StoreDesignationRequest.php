<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Add a designation. Errors go to their own bag so the page can reopen the right dialog.
 */
class StoreDesignationRequest extends FormRequest
{
    /**
     * @var string
     */
    protected $errorBag = 'createDesignation';

    /**
     * Who may manage designations is decided by the route (signed-in admin or tech).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Collapse stray spaces so "Assistant  Professor" and "Assistant Professor" count as the same.
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
                "regex:/^[\pL\pM][\pL\pM\s.&()'-]*$/u",
                Rule::unique('designations', 'name')->ignore($this->route('designation')),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Enter a designation, e.g. Assistant Professor.',
            'name.min' => 'That designation is too short.',
            'name.max' => 'Keep the designation under 60 characters.',
            'name.regex' => 'Use letters and spaces only (& . ( ) - are fine too).',
            'name.unique' => 'This designation is already listed.',
        ];
    }
}
