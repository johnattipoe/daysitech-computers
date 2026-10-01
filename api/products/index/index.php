<?php

require_once dirname(__DIR__, 2) . '/_bootstrap.php';

use App\Models\Product;
use App\Models\Category;
use App\Services\ProductService;

api_require_method('GET');
$slug = trim((string) ($_GET['slug'] ?? ''));

if (!empty($_GET['categories'])) {
	api_success(['categories' => Category::active()]);
}

if (!empty($_GET['featured'])) {
	api_success(['products' => Product::featured(api_limit())]);
}

if ($slug !== '') {
	$product = Product::findBySlug($slug);
	if (!$product || empty($product['is_active'])) api_error('Product not found.', 404);
	api_success(['product' => (new ProductService())->withReviewSummary($product)]);
}

$filters = [
	'q' => trim((string) ($_GET['q'] ?? '')),
	'category_id' => trim((string) ($_GET['category'] ?? '')),
	'brand_id' => trim((string) ($_GET['brand'] ?? '')),
	'min_price' => $_GET['min_price'] ?? '',
	'max_price' => $_GET['max_price'] ?? '',
	'in_stock' => $_GET['in_stock'] ?? '',
	'sort' => $_GET['sort'] ?? 'newest',
];
$service = new ProductService();
$results = $service->search($filters);
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = max(1, min(100, (int) ($_GET['per_page'] ?? 20)));
api_success($service->paginate($results, $page, $perPage) + ['filters' => $filters]);
