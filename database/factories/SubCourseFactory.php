<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\SubCourse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubCourse>
 */
class SubCourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'name' => ucfirst(fake()->unique()->word()).' Technology',
        ];
    }
}
