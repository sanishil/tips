<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The 6-digit code for signing up ("register") or resetting a password ("reset").
 */
class OtpCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code, public string $purpose) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->purpose === 'reset'
                ? "{$this->code} is your password reset code"
                : "{$this->code} is your sign-up code",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.otp-code',
            with: [
                'action' => $this->purpose === 'reset' ? 'reset your password' : 'finish creating your account',
            ],
        );
    }
}
