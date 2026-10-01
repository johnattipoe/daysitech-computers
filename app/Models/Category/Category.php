<?php

namespace App\Models;

/**
 * Category
 * Fields: id, name, slug, icon, image, parent_id, is_active, sort_order
 */
class Category extends Model
{
    protected static string $collectionKey = 'categories';

    public static function active(): array
    {
        return static::where([['field' => 'is_active', 'op' => 'EQUAL', 'value' => true]], 'sort_order:asc', 100);
    }

    public static function findBySlug(string $slug): ?array
    {
        $r = static::where([['field' => 'slug', 'op' => 'EQUAL', 'value' => $slug]], null, 1);
        return $r[0] ?? null;
    }
}
