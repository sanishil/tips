<?php

namespace Database\Factories;

use App\Models\AuthToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<AuthToken>
 */
class AuthTokenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jti' => (string) Str::uuid(),
            'user_id' => User::factory(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'last_used_at' => now(),
            'expires_at' => now()->addHours(2),
        ];
    }

    /**
     * A token whose lifetime has run out.
     */
    public function expired(): static
    {
        return $this->state(fn (): array => [
            'last_used_at' => now()->subHours(3),
            'expires_at' => now()->subHour(),
        ]);
    }
}
