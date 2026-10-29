<?php

namespace App\Models;

use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A course level (e.g. UG, PG), split into sub-courses (e.g. UG → Paramedical, PG → MLT).
 */
#[Fillable(['name'])]
class Course extends Model
{
    /** @use HasFactory<CourseFactory> */
    use HasFactory;

    /**
     * @return HasMany<SubCourse, $this>
     */
    public function subCourses(): HasMany
    {
        return $this->hasMany(SubCourse::class);
    }
}
