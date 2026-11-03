<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Teacher>
 */
class TeacherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'name' => 'Dr. '.fake()->firstName().' '.fake()->lastName(),
        ];
    }

    /**
     * Give every teacher a designation unless the test attached its own (with hasAttached).
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Teacher $teacher): void {
            if ($teacher->designations()->doesntExist()) {
                $teacher->designations()->attach(Designation::factory()->create());
            }
        });
    }
}
