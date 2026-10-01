<?php

namespace App\Models;

/**
 * Cart
 * Guests: stored entirely in $_SESSION['cart'] = [productId => ['qty' => n, ...]]
 * Logged-in users: mirrored to Firestore (collection "carts", doc id = user id)
 * so the cart survives across devices.
 */
class Cart extends Model
{
    protected static string $collectionKey = 'carts';

    public static function items(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    public static function add(string $productId, int $qty = 1): void
    {
        $cart = self::items();
        $cart[$productId] = ['qty' => ($cart[$productId]['qty'] ?? 0) + $qty];
        $_SESSION['cart'] = $cart;
        self::persist();
    }

    public static function setQty(string $productId, int $qty): void
    {
        $cart = self::items();
        if ($qty <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = ['qty' => $qty];
        }
        $_SESSION['cart'] = $cart;
        self::persist();
    }

    public static function remove(string $productId): void
    {
        $cart = self::items();
        unset($cart[$productId]);
        $_SESSION['cart'] = $cart;
        self::persist();
    }

    public static function clear(): void
    {
        $_SESSION['cart'] = [];
        if ($user = current_user()) {
            static::update($user['id'], ['items' => []]);
        }
    }

    public static function count(): int
    {
        return array_sum(array_column(self::items(), 'qty'));
    }

    /** Returns [['product' => [...], 'qty' => n, 'subtotal' => x], ...] */
    public static function detailed(): array
    {
        $out = [];
        foreach (self::items() as $productId => $row) {
            $product = Product::find($productId);
            if (!$product) continue;
            $out[] = [
                'product'  => $product,
                'qty'      => $row['qty'],
                'subtotal' => (float) $product['price'] * $row['qty'],
            ];
        }
        return $out;
    }

    public static function total(): float
    {
        return array_sum(array_column(self::detailed(), 'subtotal'));
    }

    /** Save the session cart to Firestore for the logged-in user (best effort). */
    protected static function persist(): void
    {
        $user = current_user();
        if (!$user) return;
        static::db()->update(static::collection(), $user['id'], ['items' => self::items()]);
    }

    /** Pull a previously saved cart from Firestore into the session, e.g. on login. */
    public static function restoreForUser(string $userId): void
    {
        $doc = static::find($userId);
        if ($doc && !empty($doc['items'])) {
            $_SESSION['cart'] = $doc['items'];
        }
    }
}
