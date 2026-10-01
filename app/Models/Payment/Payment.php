<?php

namespace App\Models;

/**
 * Payment
 * Fields: id, order_id, user_id, amount, method, provider_reference,
 *         status (pending/success/failed), gateway_response, created_at
 */
class Payment extends Model
{
    protected static string $collectionKey = 'payments';

    public static function forOrder(string $orderId): array
    {
        return static::where([['field' => 'order_id', 'op' => 'EQUAL', 'value' => $orderId]], 'created_at:desc', 10);
    }

    public static function recent(int $limit = 50): array
    {
        return static::where([], 'created_at:desc', $limit);
    }

    public static function markStatus(string $id, string $status, array $gatewayResponse = []): array
    {
        return static::update($id, ['status' => $status, 'gateway_response' => $gatewayResponse]);
    }
}
