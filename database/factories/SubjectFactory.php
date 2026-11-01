<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $semester = fake()->numberBetween(1, Subject::MAX_SEMESTER);

        return [
            'department_id' => Department::factory(),
            'semester' => $semester,
            'name' => ucfirst(fake()->unique()->words(3, true)),
            'code' => strtoupper(fake()->unique()->lexify('???')).'-'.$semester.fake()->numerify('##'),
        ];
    }
}
