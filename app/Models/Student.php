<?php

namespace App\Models;

use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A student, saved (or updated) by university roll number each time they print a cover page, so the form can fill
 * itself in next time.
 */
#[Fillable(['tu_roll', 'title', 'name', 'department_id', 'semester', 'section'])]
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
     * @return array{tu_roll: string, title: string, name: string, std_dept: ?string, sem: int, section: ?string}
     */
    public function toCoverForm(): array
    {
        return [
            'tu_roll' => $this->tu_roll,
            'title' => $this->title,
            'name' => $this->name,
            'std_dept' => $this->department?->dept_name,
            'sem' => $this->semester,
            'section' => $this->section,
        ];
    }
}
