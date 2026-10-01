<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Review;

class ProductService
{
    /** Basic in-memory search/filter over the active catalog (Firestore has limited text search). */
    public function search(array $filters = []): array
    {
        $products = Product::active(500);

        if (!empty($filters['q'])) {
            $q = mb_strtolower($filters['q']);
            $products = array_filter($products, fn($p) =>
                str_contains(mb_strtolower($p['name'] ?? ''), $q) ||
                str_contains(mb_strtolower($p['description'] ?? ''), $q) ||
                str_contains(mb_strtolower($p['sku'] ?? ''), $q)
            );
        }

        if (!empty($filters['category_id'])) {
            $products = array_filter($products, fn($p) => ($p['category_id'] ?? null) === $filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $products = array_filter($products, fn($p) => ($p['brand_id'] ?? null) === $filters['brand_id']);
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $products = array_filter($products, fn($p) => (float) $p['price'] >= (float) $filters['min_price']);
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $products = array_filter($products, fn($p) => (float) $p['price'] <= (float) $filters['max_price']);
        }

        if (!empty($filters['in_stock'])) {
            $products = array_filter($products, fn($p) => (int) ($p['stock'] ?? 0) > 0);
        }

        $products = array_values($products);

        $sort = $filters['sort'] ?? 'newest';
        usort($products, function ($a, $b) use ($sort) {
            return match ($sort) {
                'price_low'  => $a['price'] <=> $b['price'],
                'price_high' => $b['price'] <=> $a['price'],
                'rating'     => ($b['rating_avg'] ?? 0) <=> ($a['rating_avg'] ?? 0),
                'name'       => strcasecmp($a['name'], $b['name']),
                default      => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''),
            };
        });

        return $products;
    }

    public function paginate(array $items, int $page, int $perPage): array
    {
        $total = count($items);
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        return [
            'items'       => array_slice($items, $offset, $perPage),
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => (int) max(1, ceil($total / $perPage)),
        ];
    }

    public function withReviewSummary(array $product): array
    {
        $reviews = Review::forProduct($product['id']);
        $product['reviews'] = $reviews;
        $product['review_count'] = count($reviews);
        $product['review_avg'] = Review::averageRating($reviews);
        return $product;
    }

    public function related(array $product, int $limit = 4): array
    {
        $siblings = Product::byCategory($product['category_id'] ?? '', $limit + 1);
        return array_values(array_filter($siblings, fn($p) => $p['id'] !== $product['id']));
    }

    public function filterOptions(): array
    {
        return [
            'categories' => Category::active(),
            'brands'     => Brand::active(),
        ];
    }
}
