<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function push(string $userId, string $type, string $title, string $message, ?string $link = null): array
    {
        return Notification::push($userId, $type, $title, $message, $link);
    }

    public function orderPlaced(string $userId, array $order): array
    {
        return $this->push($userId, NOTIFY_ORDER, 'Order placed', "Order {$order['order_number']} has been placed successfully.", "/account/orders");
    }

    public function orderStatusChanged(string $userId, array $order): array
    {
        return $this->push($userId, NOTIFY_ORDER, 'Order updated', "Order {$order['order_number']} is now " . status_label($order['status']) . ".", "/account/orders");
    }

    public function forUser(string $userId): array
    {
        return Notification::forUser($userId);
    }

    public function unreadCount(string $userId): int
    {
        return Notification::unreadCount($userId);
    }
}
