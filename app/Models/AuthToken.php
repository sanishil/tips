<?php

namespace App\Models;

use Database\Factories\AuthTokenFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A signed-in browser: the JWT in its cookie names this row by jti. Deleting the row signs that browser out.
 */
#[Fillable(['jti', 'user_id', 'ip_address', 'user_agent', 'last_used_at', 'expires_at'])]
class AuthToken extends Model
{
    /** @use HasFactory<AuthTokenFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
