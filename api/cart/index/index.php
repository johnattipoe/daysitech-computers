<?php

require_once dirname(__DIR__, 2) . '/_bootstrap.php';

use App\Models\Cart;
use App\Models\Product;

$method = api_method();

if ($method === 'GET') {
	api_success(['items' => Cart::detailed(), 'count' => Cart::count(), 'total' => Cart::total()]);
}

api_require_method('POST');
$input = api_input();
$action = strtolower((string) ($input['action'] ?? 'add'));
$productId = trim((string) ($input['product_id'] ?? ''));

if ($action === 'clear') {
	Cart::clear();
	api_success(['items' => [], 'count' => 0, 'total' => 0]);
}

if ($productId === '') api_error('product_id is required.', 422);

if ($action === 'add') {
	$product = Product::find($productId);
	$qty = max(1, (int) ($input['qty'] ?? 1));
	if (!$product || !Product::isInStock($product)) api_error('Product is unavailable.', 404);
	if ($qty > (int) ($product['stock'] ?? 0)) api_error('Requested quantity is not available.', 422);
	Cart::add($productId, $qty);
} elseif ($action === 'update') {
	Cart::setQty($productId, (int) ($input['qty'] ?? 0));
} elseif ($action === 'remove') {
	Cart::remove($productId);
} else {
	api_error('Unknown cart action.', 422);
}

api_success(['items' => Cart::detailed(), 'count' => Cart::count(), 'total' => Cart::total()]);
