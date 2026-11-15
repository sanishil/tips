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
            'designation_id' => Designation::factory(),
            'name' => 'Dr. '.fake()->firstName().' '.fake()->lastName(),
        ];
    }
}
