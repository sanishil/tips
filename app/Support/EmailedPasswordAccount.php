<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Mail\AccountPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Creates an account with a random password and emails it. The account must choose its own password on first sign-in.
 */
class EmailedPasswordAccount
{
    /**
     * The emailed password is digits only, so it's easy to type; it must be changed on first sign-in.
     */
    public const PASSWORD_LENGTH = 8;

    /**
     * Nothing is saved if the email can't be sent.
     *
     * @throws TransportExceptionInterface
     */
    public function create(string $name, string $email, UserRole $role, bool $emailVerified = false): User
    {
        $password = Str::password(self::PASSWORD_LENGTH, letters: false, symbols: false);

        return DB::transaction(function () use ($name, $email, $role, $emailVerified, $password): User {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role' => $role,
                'must_change_password' => true,
                'email_verified_at' => $emailVerified ? now() : null,
            ]);

            Mail::to($email)->send(new AccountPasswordMail($name, $password));

            return $user;
        });
    }
}
