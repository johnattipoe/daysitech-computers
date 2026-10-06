<?php

use App\Models\Product;
use App\Services\InventoryService;

$inventory = new InventoryService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $result = $_POST['mode'] === 'set'
        ? $inventory->adjust($_POST['product_id'], (int) $_POST['quantity'], $_POST['reason'] ?: 'Manual adjustment')
        : $inventory->restock($_POST['product_id'], (int) $_POST['quantity'], $_POST['reason'] ?: 'Manual restock');

    flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Stock updated successfully.' : $result['message']);
    redirect('/admin/inventory');
}

$products = Product::all(500);
$preselect = $_GET['product'] ?? '';

$title = 'Restock Product';
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <form action="/admin/inventory/stock" method="POST" class="admin-panel" data-stock-preview data-unsaved-warning>
            <div class="admin-panel-body">
                <h6 class="mb-3">Update Stock</h6>
                <div class="mb-3">
                    <label class="form-label">Product</label>
                    <select name="product_id" class="form-select" required>
                        <option value="">Select a product</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= e($p['id']) ?>" data-stock="<?= (int) ($p['stock'] ?? 0) ?>" <?= $preselect === $p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?> (current: <?= (int) $p['stock'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Action</label>
                    <select name="mode" class="form-select" data-stock-mode>
                        <option value="add">Add to current stock</option>
                        <option value="set">Set exact quantity</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" min="1" step="1" inputmode="numeric" data-stock-quantity required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Reason (optional)</label>
                    <input type="text" name="reason" class="form-control" placeholder="e.g. New shipment, damaged unit, stock count correction">
                </div>
                <button type="submit" class="btn btn-copper w-100">Save Stock Update</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
