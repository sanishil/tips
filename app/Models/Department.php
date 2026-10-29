<?php

namespace App\Models;

use Database\Factories\DepartmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A department of the institute (e.g. Optometry). Subjects, teachers and students will belong to one.
 */
#[Fillable(['dept_name', 'hod'])]
class Department extends Model
{
    /** @use HasFactory<DepartmentFactory> */
    use HasFactory;
}
