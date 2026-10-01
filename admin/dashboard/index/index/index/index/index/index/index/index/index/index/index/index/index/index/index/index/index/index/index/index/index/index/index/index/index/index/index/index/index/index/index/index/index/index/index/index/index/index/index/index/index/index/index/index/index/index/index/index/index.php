<?php

use App\Models\Order;
use App\Models\Repair;
use App\Models\Product;
use App\Models\User;

$orders = Order::recent(200);
$repairs = Repair::active(200);
$lowStock = Product::lowStock();
$customers = User::customers(500);

$revenue = Order::totalRevenue($orders);
$pendingOrders = count(array_filter($orders, fn($o) => $o['status'] === ORDER_STATUS_PENDING));
$activeRepairs = count($repairs);

// Build a simple last-7-days revenue series for the chart
$days = [];
$series = [];
for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-{$i} days"));
    $days[] = date('D', strtotime($day));
    $dayTotal = 0;
    foreach ($orders as $o) {
        if (($o['payment_status'] ?? '') === 'paid' && str_starts_with($o['created_at'] ?? '', $day)) {
            $dayTotal += (float) $o['total'];
        }
    }
    $series[] = $dayTotal;
}

$recentOrders = array_slice($orders, 0, 6);
$recentRepairs = array_slice($repairs, 0, 6);

$title = 'Dashboard';
ob_start();
?>

<div class="stat-grid">
    <div class="stat-card">
        <div><span class="stat-value"><?= money($revenue) ?></span><span class="stat-label">Total Revenue (Paid)</span></div>
        <div class="stat-icon" style="background:rgba(47,190,133,.12);color:#2FBE85;"><i class="fa-solid fa-sack-dollar"></i></div>
    </div>
    <div class="stat-card">
        <div><span class="stat-value"><?= count($orders) ?></span><span class="stat-label">Total Orders</span></div>
        <div class="stat-icon" style="background:rgba(201,121,61,.12);color:#C9793D;"><i class="fa-solid fa-cart-shopping"></i></div>
    </div>
    <div class="stat-card">
        <div><span class="stat-value"><?= $activeRepairs ?></span><span class="stat-label">Active Repairs</span></div>
        <div class="stat-icon" style="background:rgba(227,167,59,.15);color:#E3A73B;"><i class="fa-solid fa-screwdriver-wrench"></i></div>
    </div>
    <div class="stat-card">
        <div><span class="stat-value"><?= count($customers) ?></span><span class="stat-label">Registered Customers</span></div>
        <div class="stat-icon" style="background:rgba(11,31,58,.1);color:#0B1F3A;"><i class="fa-solid fa-users"></i></div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-panel">
            <div class="admin-panel-head"><h3>Revenue — Last 7 Days</h3></div>
            <div class="admin-panel-body">
                <canvas id="revenueChart" height="90"></canvas>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head"><h3>Recent Orders</h3><a href="/admin/orders" class="small">View all</a></div>
            <div class="admin-panel-body p-0">
                <?php if (empty($recentOrders)): ?>
                    <div class="empty-state"><i class="fa-solid fa-box-open"></i><h4>No orders yet</h4></div>
                <?php else: ?>
                    <table class="table-dtc w-100">
                        <tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th><th></th></tr>
                        <?php foreach ($recentOrders as $o): ?>
                            <tr>
                                <td class="mono"><?= e($o['order_number']) ?></td>
                                <td><?= e($o['customer']['name'] ?? '—') ?></td>
                                <td><?= money($o['total']) ?></td>
                                <td><span class="badge-pill <?= status_badge_class($o['status']) ?>"><?= status_label($o['status']) ?></span></td>
                                <td><a href="/admin/orders/<?= e($o['id']) ?>" class="btn btn-sm btn-ink">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-panel">
            <div class="admin-panel-head"><h3>Low Stock Alerts</h3></div>
            <div class="admin-panel-body p-0">
                <?php if (empty($lowStock)): ?>
                    <div class="empty-state"><i class="fa-solid fa-check"></i><h4>All stocked up</h4></div>
                <?php else: ?>
                    <table class="table-dtc w-100">
                        <?php foreach (array_slice($lowStock, 0, 8) as $p): ?>
                            <tr>
                                <td><?= e($p['name']) ?></td>
                                <td style="text-align:right;"><span class="badge-pill badge-warning"><?= (int) $p['stock'] ?> left</span></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head"><h3>Active Repair Tickets</h3><a href="/admin/repairs" class="small">View all</a></div>
            <div class="admin-panel-body p-0">
                <?php if (empty($recentRepairs)): ?>
                    <div class="empty-state"><i class="fa-solid fa-screwdriver-wrench"></i><h4>No active repairs</h4></div>
                <?php else: ?>
                    <table class="table-dtc w-100">
                        <?php foreach ($recentRepairs as $r): ?>
                            <tr>
                                <td class="mono small"><?= e($r['ticket_number']) ?></td>
                                <td style="text-align:right;"><span class="badge-pill <?= status_badge_class($r['status']) ?>"><?= status_label($r['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($days) ?>,
        datasets: [{
            label: 'Revenue (<?= config('app.currency') ?>)',
            data: <?= json_encode($series) ?>,
            borderColor: '#C9793D',
            backgroundColor: 'rgba(201,121,61,.12)',
            tension: 0.35,
            fill: true,
        }],
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } },
});
</script>
<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
