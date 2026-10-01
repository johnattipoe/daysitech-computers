<?php

require_once dirname(__DIR__, 2) . '/_bootstrap.php';

use App\Models\Order;

$user = api_require_auth();
$method = api_method();

if ($method === 'POST') {
	$input = api_input();
	$action = strtolower((string) ($input['action'] ?? ''));
	if ($action !== 'cancel') api_error('Unknown order action.', 422);
	$id = trim((string) ($input['id'] ?? ''));
	$order = $id !== '' ? Order::find($id) : null;
	if (!$order || (($order['user_id'] ?? '') !== ($user['id'] ?? '') && !in_array($user['role'] ?? '', ['admin', 'staff'], true))) {
		api_error('Order not found.', 404);
	}
	$result = (new \App\Services\OrderService())->cancel($id, trim((string) ($input['reason'] ?? '')));
	if (!$result['success']) api_error($result['message'], 422);
	api_success(['order' => $result['order']]);
}

api_require_method('GET', 'POST');

$orderNumber = trim((string) ($_GET['order'] ?? $_GET['order_number'] ?? ''));
if ($orderNumber !== '') {
	$order = Order::findByOrderNumber($orderNumber);
	$isOwner = $order && (($order['user_id'] ?? '') === ($user['id'] ?? ''));
	$isAdmin = in_array($user['role'] ?? '', ['admin', 'staff'], true);
	if (!$order || (!$isOwner && !$isAdmin)) api_error('Order not found.', 404);
	api_success(['order' => $order]);
}

$orders = Order::forUser($user['id'], api_limit());
api_success(['orders' => $orders, 'count' => count($orders)]);
