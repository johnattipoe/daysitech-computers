<?php

use App\Models\Order;

$status = $_GET['status'] ?? '';
$orders = $status ? Order::byStatus($status, 200) : Order::recent(200);

$title = 'Orders';
ob_start();
?>

<div class="admin-panel">
    <div class="admin-panel-head">
        <h3>All Orders (<?= count($orders) ?>)</h3>
        <form action="/admin/orders" method="GET">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <?php foreach (ORDER_STATUSES as $s): ?>
                    <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= status_label($s) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <div class="admin-panel-body p-0">
        <?php if (empty($orders)): ?>
            <div class="empty-state"><i class="fa-solid fa-cart-shopping"></i><h4>No orders found</h4></div>
        <?php else: ?>
            <table class="table-dtc w-100">
                <tr><th>Order #</th><th>Customer</th><th>Date</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th><th></th></tr>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td class="mono"><?= e($o['order_number']) ?></td>
                        <td><?= e($o['customer']['name'] ?? '—') ?><br><span class="text-muted-dtc small"><?= e($o['customer']['phone'] ?? '') ?></span></td>
                        <td><?= format_date($o['created_at']) ?></td>
                        <td><?= count($o['items'] ?? []) ?></td>
                        <td class="mono"><?= money($o['total']) ?></td>
                        <td><span class="badge-pill <?= ($o['payment_status'] ?? '') === 'paid' ? 'badge-success' : 'badge-warning' ?>"><?= status_label($o['payment_status'] ?? 'pending') ?></span></td>
                        <td><span class="badge-pill <?= status_badge_class($o['status']) ?>"><?= status_label($o['status']) ?></span></td>
                        <td><a href="/admin/orders/<?= e($o['id']) ?>" class="btn btn-sm btn-ink">Manage</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
