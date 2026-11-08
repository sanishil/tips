<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * A site-wide setting the admin portal can change, stored by key.
 */
#[Fillable(['key', 'value'])]
class Setting extends Model
{
    /**
     * The price of one cover page in paise; 0 means printing is free.
     */
    public const COVER_PRICE = 'cover_price_paise';

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    public static function value(string $key, ?string $default = null): ?string
    {
        return static::whereKey($key)->value('value') ?? $default;
    }

    public static function put(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * The price of one cover page, in paise.
     */
    public static function coverPrice(): int
    {
        return (int) static::value(self::COVER_PRICE, '0');
    }
}
