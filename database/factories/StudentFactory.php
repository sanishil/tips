<?php

namespace Database\Factories;

use App\Http\Controllers\CoverController;
use App\Models\Department;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tu_roll' => fake()->unique()->numerify('##########'),
            'title' => fake()->randomElement(CoverController::TITLES),
            'name' => fake()->name(),
            'department_id' => Department::factory(),
            'semester' => fake()->numberBetween(1, Subject::MAX_SEMESTER),
            'section' => fake()->randomElement(CoverController::SECTIONS),
        ];
    }
}
