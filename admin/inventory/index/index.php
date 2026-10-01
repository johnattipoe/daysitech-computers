<?php

use App\Models\Product;
use App\Services\InventoryService;

$inventory = new InventoryService();
$products = Product::all(500);
$lowStock = $inventory->lowStockAlerts();
$totalUnits = array_sum(array_column($products, 'stock'));
$totalValue = array_sum(array_map(fn($p) => (float) $p['price'] * (int) $p['stock'], $products));

$title = 'Inventory';
ob_start();
?>

<div class="stat-grid">
    <div class="stat-card">
        <div><span class="stat-value"><?= count($products) ?></span><span class="stat-label">Total SKUs</span></div>
        <div class="stat-icon" style="background:rgba(11,31,58,.1);color:#0B1F3A;"><i class="fa-solid fa-barcode"></i></div>
    </div>
    <div class="stat-card">
        <div><span class="stat-value"><?= $totalUnits ?></span><span class="stat-label">Units in Stock</span></div>
        <div class="stat-icon" style="background:rgba(47,190,133,.12);color:#2FBE85;"><i class="fa-solid fa-boxes-stacked"></i></div>
    </div>
    <div class="stat-card">
        <div><span class="stat-value"><?= money($totalValue) ?></span><span class="stat-label">Stock Value</span></div>
        <div class="stat-icon" style="background:rgba(201,121,61,.12);color:#C9793D;"><i class="fa-solid fa-sack-dollar"></i></div>
    </div>
    <div class="stat-card">
        <div><span class="stat-value"><?= count($lowStock) ?></span><span class="stat-label">Low Stock Items</span></div>
        <div class="stat-icon" style="background:rgba(227,167,59,.15);color:#E3A73B;"><i class="fa-solid fa-triangle-exclamation"></i></div>
    </div>
</div>

<div class="d-flex gap-2 mb-4">
    <a href="/admin/inventory/stock" class="btn btn-copper btn-sm"><i class="fa-solid fa-plus me-1"></i>Restock Product</a>
    <a href="/admin/inventory/movements" class="btn btn-ink btn-sm"><i class="fa-solid fa-clock-rotate-left me-1"></i>Movement History</a>
</div>

<div class="admin-panel">
    <div class="admin-panel-head"><h3>Low Stock Alerts</h3></div>
    <div class="admin-panel-body p-0">
        <?php if (empty($lowStock)): ?>
            <div class="empty-state"><i class="fa-solid fa-check"></i><h4>All products are well stocked</h4></div>
        <?php else: ?>
            <table class="table-dtc w-100">
                <tr><th>Product</th><th>Stock</th><th></th></tr>
                <?php foreach ($lowStock as $p): ?>
                    <tr>
                        <td><?= e($p['name']) ?></td>
                        <td><span class="badge-pill <?= (int) $p['stock'] === 0 ? 'badge-danger' : 'badge-warning' ?>"><?= (int) $p['stock'] ?> units</span></td>
                        <td><a href="/admin/inventory/stock?product=<?= e($p['id']) ?>" class="btn btn-sm btn-copper">Restock</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
