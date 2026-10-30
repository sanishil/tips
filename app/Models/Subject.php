<?php

namespace App\Models;

use Database\Factories\SubjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A subject taught in one semester of a course, run by a department (e.g. NUR-302 in UG → Nursing, semester 3).
 * The sub-course is only set when the course has sub-courses; a course without any (e.g. PG) leaves it empty.
 * Students only see the subjects for their own department, course, sub-course and semester.
 */
#[Fillable(['department_id', 'course_id', 'sub_course_id', 'semester', 'name', 'code'])]
class Subject extends Model
{
    /** @use HasFactory<SubjectFactory> */
    use HasFactory;

    /**
     * Semesters run from 1 up to this.
     */
    public const MAX_SEMESTER = 10;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'semester' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return BelongsTo<SubCourse, $this>
     */
    public function subCourse(): BelongsTo
    {
        return $this->belongsTo(SubCourse::class);
    }
}
