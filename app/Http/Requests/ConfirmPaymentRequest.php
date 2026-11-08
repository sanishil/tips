<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * What Razorpay Checkout hands the browser after a successful payment, sent on so the server can check the signature.
 */
class ConfirmPaymentRequest extends FormRequest
{
    /**
     * Anyone who paid on the cover page may confirm; the signature proves the payment is real.
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
            'razorpay_order_id' => ['required', 'string', 'max:40'],
            'razorpay_payment_id' => ['required', 'string', 'max:40'],
            'razorpay_signature' => ['required', 'string', 'max:128'],
        ];
    }
}
