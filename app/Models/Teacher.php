<?php

namespace App\Models;

use Database\Factories\TeacherFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * A teacher in one department, with one or more designations. Students pick from these under "Submitted to" on the
 * cover page.
 */
#[Fillable(['department_id', 'name'])]
class Teacher extends Model
{
    /** @use HasFactory<TeacherFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @return BelongsToMany<Designation, $this>
     */
    public function designations(): BelongsToMany
    {
        return $this->belongsToMany(Designation::class)->orderBy('name');
    }

    /**
     * The designations as printed on the cover page, e.g. "Lecturer (SG-I) & Principal (i/c)".
     */
    public function designationTitle(): string
    {
        return $this->designations->pluck('name')->join(' & ');
    }
}
