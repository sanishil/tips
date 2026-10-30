<?php

namespace Database\Factories;

use App\Models\AssignmentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssignmentType>
 */
class AssignmentTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Assignment '.fake()->unique()->numberBetween(1, 999),
        ];
    }
}
