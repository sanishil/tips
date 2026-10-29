<?php

namespace App\Enums;

/**
 * Admins and tech staff each have a portal (the value is its URL and route name prefix).
 * Students sign themselves up; admins create tech and admin accounts.
 */
enum UserRole: string
{
    case Admin = 'admin';
    case Tech = 'tech';
    case Student = 'student';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Tech => 'Tech',
            self::Student => 'Student',
        };
    }

    /**
     * Where the account lands after signing in. Students have no portal yet, so they get the cover page form.
     */
    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Admin, self::Tech => $this->value.'.dashboard',
            self::Student => 'cover.form',
        };
    }

    /**
     * Roles an admin can give when creating an account.
     *
     * @return list<self>
     */
    public static function staff(): array
    {
        return [self::Tech, self::Admin];
    }
}
