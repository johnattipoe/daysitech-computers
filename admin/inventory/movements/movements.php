<?php

use App\Services\InventoryService;

$movements = (new InventoryService())->movementHistory(null, 100);

$title = 'Stock Movement History';
ob_start();
?>

<div class="admin-panel">
    <div class="admin-panel-head"><h3>Recent Stock Movements</h3></div>
    <div class="admin-panel-body p-0">
        <?php if (empty($movements)): ?>
            <div class="empty-state"><i class="fa-solid fa-clock-rotate-left"></i><h4>No movements recorded yet</h4></div>
        <?php else: ?>
            <table class="table-dtc w-100">
                <tr><th>Date</th><th>Product</th><th>Type</th><th>Qty</th><th>Reason</th><th>By</th></tr>
                <?php foreach ($movements as $m): ?>
                    <tr>
                        <td><?= format_datetime($m['created_at']) ?></td>
                        <td><?= e($m['product_name']) ?></td>
                        <td><span class="badge-pill <?= $m['type'] === STOCK_IN ? 'badge-success' : ($m['type'] === STOCK_OUT ? 'badge-danger' : 'badge-info') ?>"><?= status_label($m['type']) ?></span></td>
                        <td class="mono"><?= $m['quantity'] > 0 ? '+' : '' ?><?= $m['quantity'] ?></td>
                        <td><?= e($m['reason'] ?: '—') ?><?= $m['reference'] ? ' (' . e($m['reference']) . ')' : '' ?></td>
                        <td><?= e($m['staff_name'] ?? 'System') ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
