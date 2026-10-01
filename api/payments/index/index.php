<?php

require_once dirname(__DIR__, 2) . '/_bootstrap.php';

use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;

$user = api_require_auth();
$method = api_method();
$input = api_input();

if ($method === 'GET') {
	$orderId = trim((string) ($_GET['order_id'] ?? ''));
	if ($orderId === '') api_error('order_id is required.', 422);
	$order = Order::find($orderId);
	$isOwner = $order && (($order['user_id'] ?? '') === ($user['id'] ?? ''));
	$isAdmin = in_array($user['role'] ?? '', ['admin', 'staff'], true);
	if (!$order || (!$isOwner && !$isAdmin)) api_error('Order not found.', 404);
	api_success(['payments' => Payment::forOrder($orderId)]);
}

api_require_method('POST');
$action = strtolower((string) ($input['action'] ?? 'verify'));
$orderId = trim((string) ($input['order_id'] ?? ''));

if ($action === 'initialize') {
	$order = $orderId !== '' ? Order::find($orderId) : null;
	if (!$order || (($order['user_id'] ?? '') !== ($user['id'] ?? '') && !in_array($user['role'] ?? '', ['admin', 'staff'], true))) {
		api_error('Order not found.', 404);
	}
	$email = trim((string) ($input['email'] ?? $user['email'] ?? $order['customer']['email'] ?? ''));
	$validator = Validator::make(['email' => $email], ['email' => 'required|email']);
	if ($validator->fails()) api_error($validator->firstError(), 422);
	$result = (new PaymentService())->initialize($order, $email);
	if (empty($result['status'])) api_error($result['message'] ?? 'Payment initialization failed.', 502);
	api_success(['payment' => $result]);
}

$reference = trim((string) ($input['reference'] ?? $input['order'] ?? ''));
if ($reference === '') api_error('Payment reference is required.', 422);
$order = Order::findByOrderNumber($reference);
$isOwner = $order && (($order['user_id'] ?? '') === ($user['id'] ?? ''));
$isAdmin = in_array($user['role'] ?? '', ['admin', 'staff'], true);
if (!$order || (!$isOwner && !$isAdmin)) api_error('Order not found.', 404);
$result = (new PaymentService())->verify($reference);
api_success(['verified' => $result['success'], 'order' => $result['order']], $result['success'] ? 200 : 422);
