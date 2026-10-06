<?php

$orders = \App\Models\Order::recent(200);
$repairs = \App\Models\Repair::active(200);
$customers = \App\Models\User::customers(500);
$lowStockProducts = array_values(array_filter(\App\Models\Product::lowStock(), fn(array $product) => !empty($product['is_active'])));
$pendingOrders = array_values(array_filter($orders, fn(array $order) => ($order['status'] ?? '') === ORDER_STATUS_PENDING));
$revenue = \App\Models\Order::totalRevenue($orders);
$recentOrders = array_slice($orders, 0, 6);
$activeRepairs = array_slice($repairs, 0, 6);

$title = 'Dashboard';
ob_start();
?>
<section class="admin-welcome">
    <div>
        <h2>Welcome to your control room</h2>
        <p>Track recent sales, service work, and customer activity from one place.</p>
    </div>
    <div class="admin-quick-actions">
        <a class="btn btn-copper btn-sm" href="/admin/products/create"><i class="fa-solid fa-plus me-1"></i>Add product</a>
        <a class="btn btn-quiet btn-sm" href="/admin/inventory/stock"><i class="fa-solid fa-boxes-stacked me-1"></i>Update stock</a>
    </div>
</section>

<div class="stat-grid">
    <div class="stat-card"><div><span class="stat-value"><?= count($orders) ?></span><span class="stat-label">Recent orders</span></div><div class="stat-icon" style="background:rgba(201,121,61,.12);color:#C9793D"><i class="fa-solid fa-cart-shopping"></i></div></div>
    <div class="stat-card"><div><span class="stat-value"><?= money($revenue) ?></span><span class="stat-label">Paid revenue in recent orders</span></div><div class="stat-icon" style="background:rgba(47,190,133,.12);color:#24996A"><i class="fa-solid fa-sack-dollar"></i></div></div>
    <div class="stat-card"><div><span class="stat-value"><?= count($repairs) ?></span><span class="stat-label">Active repairs</span></div><div class="stat-icon" style="background:rgba(227,167,59,.16);color:#9a6b10"><i class="fa-solid fa-screwdriver-wrench"></i></div></div>
    <div class="stat-card"><div><span class="stat-value"><?= count($customers) ?></span><span class="stat-label">Customer records</span></div><div class="stat-icon" style="background:rgba(47,130,190,.12);color:#3574a2"><i class="fa-solid fa-users"></i></div></div>
    <div class="stat-card"><div><span class="stat-value"><?= count($lowStockProducts) ?></span><span class="stat-label">Products needing restock</span></div><div class="stat-icon" style="background:rgba(224,84,44,.12);color:#b4482d"><i class="fa-solid fa-triangle-exclamation"></i></div></div>
    <div class="stat-card"><div><span class="stat-value"><?= count($pendingOrders) ?></span><span class="stat-label">Pending recent orders</span></div><div class="stat-icon" style="background:rgba(48,130,190,.12);color:#3574a2"><i class="fa-solid fa-hourglass-half"></i></div></div>
</div>

<div class="admin-quick-actions">
    <a class="admin-quick-action" href="/admin/orders"><i class="fa-solid fa-receipt"></i>Review orders</a>
    <a class="admin-quick-action" href="/admin/orders?status=pending"><i class="fa-solid fa-hourglass-half"></i>Review pending orders (<?= count($pendingOrders) ?>)</a>
    <a class="admin-quick-action" href="/admin/repairs"><i class="fa-solid fa-screwdriver-wrench"></i>Open repair queue</a>
    <a class="admin-quick-action" href="/admin/reviews"><i class="fa-solid fa-star"></i>Moderate reviews</a>
    <a class="admin-quick-action" href="/admin/reports/sales"><i class="fa-solid fa-chart-line"></i>View reports</a>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <section class="admin-panel h-100">
            <div class="admin-panel-head"><h3>Recent orders</h3><a href="/admin/orders" class="btn btn-ink btn-sm">All orders <i class="fa-solid fa-arrow-right ms-1"></i></a></div>
            <div class="admin-panel-body p-0">
                <?php if (empty($recentOrders)): ?>
                    <div class="empty-state"><i class="fa-solid fa-cart-shopping"></i><h4>No orders yet</h4><p>New orders will appear here when customers check out.</p></div>
                <?php else: ?>
                    <table class="table-dtc w-100">
                        <tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th></th></tr>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td class="mono"><?= e($order['order_number'] ?? '') ?></td>
                                <td><?= e($order['customer']['name'] ?? '—') ?></td>
                                <td class="mono"><?= money($order['total'] ?? 0) ?></td>
                                <td><span class="badge-pill <?= status_badge_class($order['status'] ?? 'pending') ?>"><?= status_label($order['status'] ?? 'pending') ?></span></td>
                                <td><a href="/admin/orders/<?= e($order['id'] ?? '') ?>" class="btn btn-sm btn-outline-secondary">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        </section>
    </div>
    <div class="col-xl-5">
        <section class="admin-panel h-100">
            <div class="admin-panel-head"><h3>Active repair tickets</h3><a href="/admin/repairs" class="btn btn-ink btn-sm">Repair queue <i class="fa-solid fa-arrow-right ms-1"></i></a></div>
            <div class="admin-panel-body p-0">
                <?php if (empty($activeRepairs)): ?>
                    <div class="empty-state"><i class="fa-solid fa-screwdriver-wrench"></i><h4>No active repairs</h4><p>Open tickets will appear here as they are received.</p></div>
                <?php else: ?>
                    <table class="table-dtc w-100">
                        <tr><th>Ticket</th><th>Device</th><th>Status</th><th></th></tr>
                        <?php foreach ($activeRepairs as $repair): ?>
                            <tr>
                                <td class="mono"><?= e($repair['ticket_number'] ?? '') ?></td>
                                <td><?= e($repair['device_type'] ?? 'Device') ?> <?= e($repair['brand'] ?? '') ?></td>
                                <td><span class="badge-pill <?= status_badge_class($repair['status'] ?? 'pending') ?>"><?= status_label($repair['status'] ?? 'pending') ?></span></td>
                                <td><a href="/admin/repairs/<?= e($repair['id'] ?? '') ?>" class="btn btn-sm btn-outline-secondary">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<br>

<section class="admin-panel admin-stock-watch">
    <div class="admin-panel-head"><div><h3>Stock watch</h3><small class="text-muted-dtc">Active products at or below <?= (int) LOW_STOCK_THRESHOLD ?> units</small></div><a href="/admin/inventory" class="btn btn-ink btn-sm">Open inventory</a></div>
    <div class="admin-panel-body p-0">
        <?php if (empty($lowStockProducts)): ?>
            <p class="text-muted-dtc p-4 mb-0">No active products are below the stock threshold.</p>
        <?php else: ?>
            <table class="table-dtc w-100">
                <tr><th>Product</th><th>SKU</th><th>Available</th><th></th></tr>
                <?php foreach (array_slice($lowStockProducts, 0, 8) as $product): ?>
                    <tr>
                        <td><?= e($product['name'] ?? 'Product') ?></td>
                        <td class="mono"><?= e($product['sku'] ?? '—') ?></td>
                        <td><span class="badge-pill <?= (int) ($product['stock'] ?? 0) === 0 ? 'badge-danger' : 'badge-warning' ?>"><?= (int) ($product['stock'] ?? 0) ?> left</span></td>
                        <td><a href="/admin/inventory/stock?product=<?= e($product['id'] ?? '') ?>" class="btn btn-sm btn-outline-secondary">Restock</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</section>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
