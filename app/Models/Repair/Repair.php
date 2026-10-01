<?php

namespace App\Models;

/**
 * Repair
 * Fields: id, ticket_number, user_id, customer{name,email,phone}, device_type,
 *         brand, model, serial_number, issue_description, service_type,
 *         status, priority, estimated_cost, final_cost, technician_notes,
 *         drop_off_date, expected_completion, completed_at, created_at
 */
class Repair extends Model
{
    protected static string $collectionKey = 'repairs';

    public static function forUser(string $userId, int $limit = 50): array
    {
        return static::where([['field' => 'user_id', 'op' => 'EQUAL', 'value' => $userId]], 'created_at:desc', $limit);
    }

    public static function byStatus(string $status, int $limit = 100): array
    {
        return static::where([['field' => 'status', 'op' => 'EQUAL', 'value' => $status]], 'created_at:desc', $limit);
    }

    public static function findByTicket(string $ticketNumber): ?array
    {
        $r = static::where([['field' => 'ticket_number', 'op' => 'EQUAL', 'value' => $ticketNumber]], null, 1);
        return $r[0] ?? null;
    }

    public static function active(int $limit = 100): array
    {
        return static::where([['field' => 'status', 'op' => 'NOT_EQUAL', 'value' => REPAIR_STATUS_COMPLETED]], null, $limit);
    }

    public static function updateStatus(string $id, string $status, ?string $note = null): array
    {
        $data = ['status' => $status];
        if ($status === REPAIR_STATUS_COMPLETED) {
            $data['completed_at'] = date('c');
        }
        if ($note) {
            $data['technician_notes'] = $note;
        }
        return static::update($id, $data);
    }
}
