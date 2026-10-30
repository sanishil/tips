<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The emailed password: the first one for a new account, or a new one an admin sent (`isNewPassword`).
 */
class AccountPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $name, public string $password, public bool $isNewPassword = false) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isNewPassword ? 'Your new password' : 'Your account is ready',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.account-password',
        );
    }
}
