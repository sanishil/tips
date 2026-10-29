<?php

namespace App\Support;

use App\Mail\OtpCodeMail;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Six-digit email codes for sign-up ("register") and password reset ("reset").
 *
 * The pending code lives in the session, so it only works in the browser that asked for it.
 */
class EmailOtp
{
    public const EXPIRES_MINUTES = 10;

    public const MAX_ATTEMPTS = 5;

    public const RESEND_SECONDS = 60;

    public function __construct(private Session $session) {}

    /**
     * Start a new code for the email and send it.
     *
     * With $deliver false nothing is emailed, but the flow looks the same, so a reset
     * request doesn't reveal whether an account exists.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ValidationException when the email can't be sent
     */
    public function send(string $purpose, string $email, array $data = [], bool $deliver = true): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        if ($deliver) {
            try {
                Mail::to($email)->send(new OtpCodeMail($code, $purpose));
            } catch (TransportExceptionInterface $exception) {
                report($exception);

                throw ValidationException::withMessages([
                    'email' => 'We couldn\'t send the email just now. Please try again in a minute.',
                ]);
            }
        }

        $this->session->put($this->key($purpose), [
            'email' => $email,
            'data' => $data,
            'deliver' => $deliver,
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(self::EXPIRES_MINUTES)->getTimestamp(),
            'sent_at' => now()->getTimestamp(),
            'attempts' => 0,
            'verified' => false,
        ]);
    }

    /**
     * Send a fresh code to the same email.
     *
     * @throws ValidationException when asked again too soon or the email can't be sent
     */
    public function resend(string $purpose): void
    {
        $pending = $this->pending($purpose);

        if ($this->resendIn($purpose) > 0) {
            throw ValidationException::withMessages([
                'code' => 'Please wait a minute before asking for another code.',
            ]);
        }

        try {
            $this->send($purpose, $pending['email'], $pending['data'], $pending['deliver']);
        } catch (ValidationException) {
            throw ValidationException::withMessages([
                'code' => 'We couldn\'t send the email just now. Please try again in a minute.',
            ]);
        }
    }

    /**
     * Check the code the person typed. Marks the pending code as verified when it matches.
     *
     * @throws ValidationException when the code is wrong, expired or out of attempts
     */
    public function verify(string $purpose, string $code): void
    {
        $fail = fn (string $message) => throw ValidationException::withMessages(['code' => $message]);
        $pending = $this->pending($purpose) ?? $fail('That code has expired. Ask for a new one.');

        if ($pending['attempts'] >= self::MAX_ATTEMPTS) {
            $fail('Too many wrong tries. Ask for a new code.');
        }

        if (now()->getTimestamp() > $pending['expires_at']) {
            $fail('That code has expired. Ask for a new one.');
        }

        if (! Hash::check($code, $pending['code'])) {
            $pending['attempts']++;
            $this->session->put($this->key($purpose), $pending);

            $triesLeft = self::MAX_ATTEMPTS - $pending['attempts'];
            $fail($triesLeft > 0
                ? "That code isn't right. You have {$triesLeft} ".($triesLeft === 1 ? 'try' : 'tries').' left.'
                : 'That code isn\'t right. Ask for a new one.');
        }

        $pending['verified'] = true;
        $this->session->put($this->key($purpose), $pending);
    }

    /**
     * The code in progress, or null when there isn't one.
     *
     * @return array{email: string, data: array<string, mixed>, deliver: bool, code: string, expires_at: int, sent_at: int, attempts: int, verified: bool}|null
     */
    public function pending(string $purpose): ?array
    {
        return $this->session->get($this->key($purpose));
    }

    public function isVerified(string $purpose): bool
    {
        return (bool) ($this->pending($purpose)['verified'] ?? false);
    }

    /**
     * Seconds until another code may be sent.
     */
    public function resendIn(string $purpose): int
    {
        $sentAt = $this->pending($purpose)['sent_at'] ?? 0;

        return max(0, $sentAt + self::RESEND_SECONDS - now()->getTimestamp());
    }

    /**
     * The pending email with most of the name hidden, e.g. "r•••••@gmail.com".
     */
    public function maskedEmail(string $purpose): string
    {
        [$name, $domain] = explode('@', $this->pending($purpose)['email'] ?? '@', 2);

        return mb_substr($name, 0, 1).str_repeat('•', max(mb_strlen($name) - 1, 3)).'@'.$domain;
    }

    public function forget(string $purpose): void
    {
        $this->session->forget($this->key($purpose));
    }

    private function key(string $purpose): string
    {
        return "email_otp.{$purpose}";
    }
}
