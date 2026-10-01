<?php

namespace App\Models;

/** Brand — Fields: id, name, slug, logo, is_active */
class Brand extends Model
{
    protected static string $collectionKey = 'brands';

    public static function active(): array
    {
        return static::where([['field' => 'is_active', 'op' => 'EQUAL', 'value' => true]], 'name:asc', 100);
    }
}
