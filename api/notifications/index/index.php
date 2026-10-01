<?php

require_once dirname(__DIR__, 2) . '/_bootstrap.php';

use App\Models\Notification;

$user = api_require_auth();
$method = api_method();

if ($method === 'GET') {
	$notifications = Notification::forUser($user['id'], api_limit());
	api_success([
		'notifications' => $notifications,
		'unread_count' => Notification::unreadCount($user['id']),
	]);
}

api_require_method('POST');
$input = api_input();
$action = strtolower((string) ($input['action'] ?? 'read'));

if ($action === 'read-all') {
	Notification::markAllRead($user['id']);
	api_success(['message' => 'Notifications marked as read.']);
}

$id = trim((string) ($input['id'] ?? ''));
$owned = array_filter(Notification::forUser($user['id'], 100), fn(array $notification): bool => ($notification['id'] ?? '') === $id);
if ($id === '' || !$owned) api_error('Notification not found.', 404);
Notification::markRead($id);
api_success(['message' => 'Notification marked as read.']);
