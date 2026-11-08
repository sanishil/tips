<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * The price of one cover page, in rupees. 0 makes printing free; otherwise Razorpay's minimum of ₹1 applies.
 */
class UpdateCoverPriceRequest extends FormRequest
{
    /**
     * Only admins reach this route (role middleware).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'price' => ['required', 'decimal:0,2', function (string $attribute, mixed $value, Closure $fail): void {
                if ((float) $value !== 0.0 && ((float) $value < 1 || (float) $value > 10000)) {
                    $fail('Enter 0 to make printing free, or a price from ₹1 to ₹10,000.');
                }
            }],
        ];
    }

    /**
     * The price in paise.
     */
    public function priceInPaise(): int
    {
        return (int) round((float) $this->validated('price') * 100);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'price.required' => 'Enter a price, or 0 to make printing free.',
            'price.decimal' => 'Enter the price in rupees, e.g. 10 or 12.50.',
        ];
    }
}
