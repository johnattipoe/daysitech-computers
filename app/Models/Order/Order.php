<?php

namespace App\Models;

/**
 * Order
 * Fields: id, order_number, user_id, customer{name,email,phone,address,city},
 *         items[{product_id,name,price,qty,subtotal}], subtotal, shipping_fee,
 *         total, status, payment_method, payment_status, notes, created_at
 */
class Order extends Model
{
    protected static string $collectionKey = 'orders';

    public static function forUser(string $userId, int $limit = 50): array
    {
        return static::where([['field' => 'user_id', 'op' => 'EQUAL', 'value' => $userId]], 'created_at:desc', $limit);
    }

    public static function byStatus(string $status, int $limit = 100): array
    {
        return static::where([['field' => 'status', 'op' => 'EQUAL', 'value' => $status]], 'created_at:desc', $limit);
    }

    public static function findByOrderNumber(string $orderNumber): ?array
    {
        $r = static::where([['field' => 'order_number', 'op' => 'EQUAL', 'value' => $orderNumber]], null, 1);
        return $r[0] ?? null;
    }

    public static function recent(int $limit = 10): array
    {
        return static::where([], 'created_at:desc', $limit);
    }

    public static function updateStatus(string $id, string $status): array
    {
        return static::update($id, ['status' => $status]);
    }

    public static function totalRevenue(array $orders): float
    {
        return array_sum(array_map(fn($o) => (float) ($o['total'] ?? 0), array_filter($orders, fn($o) => ($o['payment_status'] ?? '') === 'paid')));
    }
}
