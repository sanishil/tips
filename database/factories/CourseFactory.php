<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'B.'.ucfirst(fake()->unique()->lexify('???')),
            'semesters' => fake()->randomElement([4, 6, 8]),
        ];
    }
}
