<?php

namespace App\Models;

use Database\Factories\SubCourseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A sub-course within a course, e.g. Paramedical under UG or MLT under PG.
 */
#[Fillable(['course_id', 'name'])]
class SubCourse extends Model
{
    /** @use HasFactory<SubCourseFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
