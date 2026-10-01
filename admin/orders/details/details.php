<?php

use App\Models\Order;
use App\Services\OrderService;

$id = $GLOBALS['adminRouteParams'][0] ?? null;
$order = $id ? Order::find($id) : null;

if (!$order) {
    flash('error', 'Order not found.');
    redirect('/admin/orders');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    (new OrderService())->updateStatus($id, $_POST['status']);
    flash('success', 'Order status updated.');
    redirect('/admin/orders/' . $id);
}

$title = 'Order ' . $order['order_number'];
ob_start();
?>

<a href="/admin/orders" class="small text-muted-dtc mb-3 d-inline-block"><i class="fa-solid fa-arrow-left me-1"></i>Back to Orders</a>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-panel">
            <div class="admin-panel-head"><h3>Order Items</h3></div>
            <div class="admin-panel-body p-0">
                <table class="table-dtc w-100">
                    <tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr>
                    <?php foreach ($order['items'] as $item): ?>
                        <tr>
                            <td><?= e($item['name']) ?></td>
                            <td class="mono"><?= money($item['price']) ?></td>
                            <td><?= $item['qty'] ?></td>
                            <td class="mono"><?= money($item['subtotal']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <div class="admin-panel-body pt-0">
                <div class="summary-line"><span>Subtotal</span><span><?= money($order['subtotal']) ?></span></div>
                <div class="summary-line"><span>Shipping</span><span><?= money($order['shipping_fee']) ?></span></div>
                <div class="summary-total"><span>Total</span><span><?= money($order['total']) ?></span></div>
            </div>
        </div>

        <?php if (!empty($order['notes'])): ?>
        <div class="admin-panel"><div class="admin-panel-body">
            <h6>Customer Notes</h6>
            <p class="mb-0 text-muted-dtc"><?= e($order['notes']) ?></p>
        </div></div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <div class="admin-panel"><div class="admin-panel-body">
            <h6 class="mb-3">Customer</h6>
            <p class="mb-1"><strong><?= e($order['customer']['name'] ?? '') ?></strong></p>
            <p class="mb-1 text-muted-dtc small"><i class="fa-solid fa-phone me-1"></i><?= e($order['customer']['phone'] ?? '') ?></p>
            <p class="mb-1 text-muted-dtc small"><i class="fa-solid fa-envelope me-1"></i><?= e($order['customer']['email'] ?? '') ?></p>
            <p class="mb-0 text-muted-dtc small"><i class="fa-solid fa-location-dot me-1"></i><?= e($order['customer']['address'] ?? '') ?>, <?= e($order['customer']['city'] ?? '') ?></p>
        </div></div>

        <div class="admin-panel"><div class="admin-panel-body">
            <h6 class="mb-3">Order Status</h6>
            <form action="/admin/orders/<?= e($id) ?>" method="POST">
                <?= csrf_field() ?>
                <select name="status" class="form-select mb-3">
                    <?php foreach (ORDER_STATUSES as $s): ?>
                        <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= status_label($s) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-copper w-100">Update Status</button>
            </form>
            <p class="text-muted-dtc small mt-3 mb-0">Payment: <span class="badge-pill <?= ($order['payment_status'] ?? '') === 'paid' ? 'badge-success' : 'badge-warning' ?>"><?= status_label($order['payment_status'] ?? 'pending') ?></span></p>
        </div></div>
    </div>
</div>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
