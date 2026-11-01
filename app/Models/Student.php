<?php

namespace App\Models;

use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A student, saved (or updated) by college ID each time they print a cover page, so the form can fill itself in
 * next time. Course and sub-course come from the department.
 */
#[Fillable(['std_id', 'tu_roll', 'title', 'name', 'department_id', 'semester', 'section'])]
class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use HasFactory;

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
     * The cover page form's fields, keyed by input name.
     *
     * @return array{std_id: string, tu_roll: ?string, title: string, name: string, course: ?string, sub_course: ?string, std_dept: ?string, sem: int, section: ?string}
     */
    public function toCoverForm(): array
    {
        return [
            'std_id' => $this->std_id,
            'tu_roll' => $this->tu_roll,
            'title' => $this->title,
            'name' => $this->name,
            'course' => $this->department?->course?->name,
            'sub_course' => $this->department?->subCourse?->name,
            'std_dept' => $this->department?->dept_name,
            'sem' => $this->semester,
            'section' => $this->section,
        ];
    }
}
