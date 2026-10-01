<?php

namespace App\Models;

use App\Services\FirebaseService;

/**
 * Base Model
 * ----------
 * Every model maps to one Firestore collection (see config/firebase.php).
 * This base class provides the common find/all/create/update/delete
 * surface so individual models stay focused on business rules.
 */
abstract class Model
{
    protected static ?FirebaseService $firebase = null;

    /** Override in child class: the config/firebase.php collections key */
    protected static string $collectionKey = '';

    protected static function db(): FirebaseService
    {
        return static::$firebase ??= new FirebaseService();
    }

    public static function collection(): string
    {
        return config('firebase.collections.' . static::$collectionKey, static::$collectionKey);
    }

    public static function find(string $id): ?array
    {
        return static::db()->get(static::collection(), $id);
    }

    public static function all(int $limit = 100): array
    {
        return static::db()->all(static::collection(), $limit);
    }

    public static function where(array $filters, ?string $orderBy = null, int $limit = 50): array
    {
        return static::db()->query(static::collection(), $filters, $orderBy, $limit);
    }

    public static function create(array $data, ?string $id = null): array
    {
        $data['created_at'] = $data['created_at'] ?? date('c');
        return static::db()->create(static::collection(), $data, $id);
    }

    public static function update(string $id, array $data): array
    {
        $data['updated_at'] = date('c');
        return static::db()->update(static::collection(), $id, $data);
    }

    public static function delete(string $id): bool
    {
        return static::db()->delete(static::collection(), $id);
    }
}
