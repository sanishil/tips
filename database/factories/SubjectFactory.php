<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Department;
use App\Models\SubCourse;
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
            'sub_course_id' => SubCourse::factory(),
            'course_id' => fn (array $attributes): int|CourseFactory => $attributes['sub_course_id'] !== null
                ? SubCourse::findOrFail($attributes['sub_course_id'])->course_id
                : Course::factory(),
            'semester' => $semester,
            'name' => ucfirst(fake()->unique()->words(3, true)),
            'code' => strtoupper(fake()->unique()->lexify('???')).'-'.$semester.fake()->numerify('##'),
        ];
    }

    /**
     * A subject of a course that has no sub-courses (e.g. PG).
     */
    public function withoutSubCourse(): static
    {
        return $this->state(fn (): array => ['sub_course_id' => null]);
    }
}
