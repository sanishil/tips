<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Checks a Google reCAPTCHA v2 token (the form's `g-recaptcha-response` field) with Google.
 */
class Recaptcha implements ValidationRule
{
    public const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Run even when the field is empty, so a missing tick fails without needing `required`.
     */
    public bool $implicit = true;

    public function __construct(private ?string $ipAddress = null) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            $fail('Please tick the box first.');

            return;
        }

        try {
            $response = Http::asForm()->timeout(5)->post(self::VERIFY_URL, array_filter([
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $value,
                'remoteip' => $this->ipAddress,
            ]));
        } catch (ConnectionException $exception) {
            report($exception);

            $fail('We couldn\'t check the "I\'m not a robot" box. Please try again.');

            return;
        }

        if ($response->json('success') !== true) {
            $fail('That check timed out. Please tick the box again.');
        }
    }
}
