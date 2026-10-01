<?php

namespace App\Models;

/**
 * Product
 * Fields: id, name, slug, sku, description, short_description, price,
 *         compare_price, category_id, brand_id, images[], specs{}, stock,
 *         is_featured, is_active, rating_avg, rating_count, created_at
 */
class Product extends Model
{
    protected static string $collectionKey = 'products';

    public static function active(int $limit = 100): array
    {
        return static::where([['field' => 'is_active', 'op' => 'EQUAL', 'value' => true]], 'created_at:desc', $limit);
    }

    public static function featured(int $limit = 8): array
    {
        return static::where([
            ['field' => 'is_active', 'op' => 'EQUAL', 'value' => true],
            ['field' => 'is_featured', 'op' => 'EQUAL', 'value' => true],
        ], null, $limit);
    }

    public static function byCategory(string $categoryId, int $limit = 50): array
    {
        return static::where([
            ['field' => 'category_id', 'op' => 'EQUAL', 'value' => $categoryId],
            ['field' => 'is_active', 'op' => 'EQUAL', 'value' => true],
        ], null, $limit);
    }

    public static function findBySlug(string $slug): ?array
    {
        $r = static::where([['field' => 'slug', 'op' => 'EQUAL', 'value' => $slug]], null, 1);
        return $r[0] ?? null;
    }

    public static function lowStock(int $threshold = LOW_STOCK_THRESHOLD): array
    {
        return static::where([['field' => 'stock', 'op' => 'LESS_THAN_OR_EQUAL', 'value' => $threshold]], 'stock:asc', 100);
    }

    public static function decrementStock(string $id, int $qty): void
    {
        $product = static::find($id);
        if (!$product) return;
        $newStock = max(0, (int) ($product['stock'] ?? 0) - $qty);
        static::update($id, ['stock' => $newStock]);
    }

    public static function incrementStock(string $id, int $qty): void
    {
        $product = static::find($id);
        if (!$product) return;
        static::update($id, ['stock' => (int) ($product['stock'] ?? 0) + $qty]);
    }

    public static function isInStock(array $product): bool
    {
        return (int) ($product['stock'] ?? 0) > 0;
    }
}
