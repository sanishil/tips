<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /* The first admin. Safe to run again: it updates the same account. */
        User::updateOrCreate(
            ['email' => 'sanishil.cse@gmail.com'],
            [
                'name' => 'Sani Shil',
                'password' => '12345678',
                'role' => UserRole::Admin,
                'must_change_password' => false,
                'email_verified_at' => now(),
            ],
        );
    }
}
