<?php

namespace App\Models;

use Database\Factories\AssignmentTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * What the cover page is for (e.g. Assignment 1), picked under "This is a…" and printed at the top.
 */
#[Fillable(['name'])]
class AssignmentType extends Model
{
    /** @use HasFactory<AssignmentTypeFactory> */
    use HasFactory;
}
