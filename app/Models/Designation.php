<?php

namespace App\Models;

use Database\Factories\DesignationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A teacher's post (e.g. Assistant Professor), printed under their name on the cover page.
 */
#[Fillable(['name'])]
class Designation extends Model
{
    /** @use HasFactory<DesignationFactory> */
    use HasFactory;

    /**
     * @return HasMany<Teacher, $this>
     */
    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }
}
