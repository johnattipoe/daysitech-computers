<?php

require_once dirname(__DIR__, 2) . '/_bootstrap.php';

use App\Models\Inventory;

api_require_method('GET');
api_require_admin();

$productId = trim((string) ($_GET['product_id'] ?? ''));
$productId = $productId !== '' ? $productId : trim((string) ($_GET['product'] ?? ''));
$movements = $productId !== ''
	? Inventory::forProduct($productId, api_limit())
	: Inventory::recent(api_limit());

api_success(['movements' => $movements, 'count' => count($movements)]);
