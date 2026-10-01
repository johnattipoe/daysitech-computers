<?php

use App\Models\Product;
use App\Models\Category;

$q = $_GET['q'] ?? '';
$products = Product::all(500);

if ($q) {
    $needle = mb_strtolower($q);
    $products = array_filter($products, fn($p) => str_contains(mb_strtolower($p['name'] ?? ''), $needle) || str_contains(mb_strtolower($p['sku'] ?? ''), $needle));
}

usort($products, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));

$categories = Category::active();
$categoryMap = array_column($categories, 'name', 'id');

$title = 'Products';
ob_start();
?>

<div class="admin-panel">
    <div class="admin-panel-head">
        <h3>All Products (<?= count($products) ?>)</h3>
        <div class="d-flex gap-2">
            <form action="/admin/products" method="GET" class="d-flex gap-2">
                <input type="search" name="q" class="form-control form-control-sm" placeholder="Search products…" value="<?= e($q) ?>">
            </form>
            <a href="/admin/products/create" class="btn btn-copper btn-sm"><i class="fa-solid fa-plus me-1"></i>Add Product</a>
        </div>
    </div>
    <div class="admin-panel-body p-0">
        <?php if (empty($products)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-laptop"></i>
                <h4>No products yet</h4>
                <p>Add your first product to start selling.</p>
                <a href="/admin/products/create" class="btn btn-copper mt-3">Add Product</a>
            </div>
        <?php else: ?>
            <table class="table-dtc w-100">
                <tr><th></th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th></th></tr>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><img src="<?= e($p['images'][0] ?? '') ?>" class="table-thumb" onerror="this.style.visibility='hidden'"></td>
                        <td><?= e($p['name']) ?><br><span class="text-muted-dtc small mono"><?= e($p['sku'] ?? '') ?></span></td>
                        <td><?= e($categoryMap[$p['category_id'] ?? ''] ?? '—') ?></td>
                        <td class="mono"><?= money($p['price']) ?></td>
                        <td>
                            <?php $stock = (int) ($p['stock'] ?? 0); ?>
                            <span class="badge-pill <?= $stock === 0 ? 'badge-danger' : ($stock <= LOW_STOCK_THRESHOLD ? 'badge-warning' : 'badge-success') ?>"><?= $stock ?> units</span>
                        </td>
                        <td><span class="badge-pill <?= !empty($p['is_active']) ? 'badge-success' : 'badge-neutral' ?>"><?= !empty($p['is_active']) ? 'Active' : 'Hidden' ?></span></td>
                        <td class="d-flex gap-2">
                            <a href="/admin/products/edit/<?= e($p['id']) ?>" class="btn btn-sm btn-ink"><i class="fa-solid fa-pen"></i></a>
                            <form action="/admin/products/edit/<?= e($p['id']) ?>" method="POST" data-confirm="Delete this product permanently?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_action" value="delete">
                                <button type="submit" class="btn btn-sm text-danger"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
