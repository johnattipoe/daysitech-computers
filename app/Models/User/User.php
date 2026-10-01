<?php

namespace App\Models;

/**
 * User
 * Fields: id, uid (firebase auth uid), name, email, phone, role,
 *         address, city, avatar, password_hash (mirrored for staff logins
 *         that bypass Firebase Auth), status, created_at
 */
class User extends Model
{
    protected static string $collectionKey = 'users';

    public static function findByEmail(string $email): ?array
    {
        $results = static::where([['field' => 'email', 'op' => 'EQUAL', 'value' => strtolower($email)]], null, 1);
        return $results[0] ?? null;
    }

    public static function findByUid(string $uid): ?array
    {
        $results = static::where([['field' => 'uid', 'op' => 'EQUAL', 'value' => $uid]], null, 1);
        return $results[0] ?? null;
    }

    public static function customers(int $limit = 100): array
    {
        return static::where([['field' => 'role', 'op' => 'EQUAL', 'value' => 'customer']], 'created_at:desc', $limit);
    }

    public static function staffAndAdmins(int $limit = 100): array
    {
        return static::where([['field' => 'role', 'op' => 'IN', 'value' => ['staff', 'admin']]], null, $limit);
    }

    public static function toSessionArray(array $user): array
    {
        return [
            'id'    => $user['id'],
            'uid'   => $user['uid'] ?? null,
            'name'  => $user['name'] ?? '',
            'email' => $user['email'] ?? '',
            'role'  => $user['role'] ?? 'customer',
            'avatar' => $user['avatar'] ?? null,
        ];
    }
}
