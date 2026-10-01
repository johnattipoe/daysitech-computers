<?php

namespace App\Models;

/**
 * Notification
 * Fields: id, user_id (null = broadcast to admins), type, title, message,
 *         link, is_read, created_at
 */
class Notification extends Model
{
    protected static string $collectionKey = 'notifications';

    public static function forUser(string $userId, int $limit = 30): array
    {
        return static::where([['field' => 'user_id', 'op' => 'EQUAL', 'value' => $userId]], 'created_at:desc', $limit);
    }

    public static function unreadCount(string $userId): int
    {
        return count(array_filter(self::forUser($userId), fn($n) => empty($n['is_read'])));
    }

    public static function push(string $userId, string $type, string $title, string $message, ?string $link = null): array
    {
        return static::create([
            'user_id' => $userId, 'type' => $type, 'title' => $title,
            'message' => $message, 'link' => $link, 'is_read' => false,
        ]);
    }

    public static function markRead(string $id): array
    {
        return static::update($id, ['is_read' => true]);
    }

    public static function markAllRead(string $userId): void
    {
        foreach (self::forUser($userId) as $n) {
            if (empty($n['is_read'])) {
                static::update($n['id'], ['is_read' => true]);
            }
        }
    }
}
