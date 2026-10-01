<?php

namespace App\Models;

/**
 * Inventory movement log — every stock change is recorded here for audit.
 * Fields: id, product_id, product_name, type (in/out/adjustment/return),
 *         quantity, reason, reference (order/repair id), staff_id, created_at
 */
class Inventory extends Model
{
    protected static string $collectionKey = 'inventory';

    public static function log(string $productId, string $productName, string $type, int $quantity, string $reason = '', ?string $reference = null): array
    {
        $user = current_user();
        return static::create([
            'product_id'   => $productId,
            'product_name' => $productName,
            'type'         => $type,
            'quantity'     => $quantity,
            'reason'       => $reason,
            'reference'    => $reference,
            'staff_id'     => $user['id'] ?? null,
            'staff_name'   => $user['name'] ?? 'System',
        ]);
    }

    public static function forProduct(string $productId, int $limit = 50): array
    {
        return static::where([['field' => 'product_id', 'op' => 'EQUAL', 'value' => $productId]], 'created_at:desc', $limit);
    }

    public static function recent(int $limit = 50): array
    {
        return static::where([], 'created_at:desc', $limit);
    }
}
