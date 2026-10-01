<?php

namespace App\Controllers;

use App\Models\Notification;

class NotificationController
{
    public function index(): void
    {
        $user = current_user();
        json_response(['notifications' => Notification::forUser($user['id'])]);
    }

    public function markRead(string $id): void
    {
        Notification::markRead($id);
        json_response(['success' => true]);
    }

    public function markAllRead(): void
    {
        Notification::markAllRead(current_user()['id']);
        json_response(['success' => true]);
    }
}
