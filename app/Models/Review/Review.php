<?php

namespace App\Models;

/**
 * Review
 * Fields: id, product_id, user_id, customer_name, rating (1-5), title,
 *         comment, is_approved, created_at
 */
class Review extends Model
{
    protected static string $collectionKey = 'reviews';

    public static function forProduct(string $productId, bool $approvedOnly = true): array
    {
        $filters = [['field' => 'product_id', 'op' => 'EQUAL', 'value' => $productId]];
        if ($approvedOnly) {
            $filters[] = ['field' => 'is_approved', 'op' => 'EQUAL', 'value' => true];
        }
        return static::where($filters, 'created_at:desc', 100);
    }

    public static function pending(): array
    {
        return static::where([['field' => 'is_approved', 'op' => 'EQUAL', 'value' => false]], 'created_at:desc', 100);
    }

    public static function approve(string $id): array
    {
        return static::update($id, ['is_approved' => true]);
    }

    public static function averageRating(array $reviews): float
    {
        if (empty($reviews)) return 0.0;
        return round(array_sum(array_column($reviews, 'rating')) / count($reviews), 1);
    }
}
