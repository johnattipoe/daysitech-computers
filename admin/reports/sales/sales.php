<?php

use App\Models\Order;

$orders = Order::recent(1000);
$paidOrders = array_filter($orders, fn($o) => ($o['payment_status'] ?? '') === 'paid');

$totalRevenue = Order::totalRevenue($orders);
$avgOrderValue = count($paidOrders) ? $totalRevenue / count($paidOrders) : 0;

// Last 30 days revenue by day
$labels = [];
$series = [];
for ($i = 29; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-{$i} days"));
    $labels[] = date('j M', strtotime($day));
    $dayTotal = 0;
    foreach ($paidOrders as $o) {
        if (str_starts_with($o['created_at'] ?? '', $day)) $dayTotal += (float) $o['total'];
    }
    $series[] = round($dayTotal, 2);
}

// Revenue by payment method
$byMethod = [];
foreach ($paidOrders as $o) {
    $m = $o['payment_method'] ?? 'unknown';
    $byMethod[$m] = ($byMethod[$m] ?? 0) + (float) $o['total'];
}

// Status breakdown
$byStatus = [];
foreach ($orders as $o) {
    $byStatus[$o['status']] = ($byStatus[$o['status']] ?? 0) + 1;
}

$title = 'Sales Report';
ob_start();
?>

<div class="d-flex gap-2 mb-4">
    <a href="/admin/reports/sales" class="btn btn-copper btn-sm">Sales</a>
    <a href="/admin/reports/inventory" class="btn btn-ink btn-sm">Inventory</a>
    <a href="/admin/reports/repairs" class="btn btn-ink btn-sm">Repairs</a>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div><span class="stat-value"><?= money($totalRevenue) ?></span><span class="stat-label">Total Revenue</span></div>
        <div class="stat-icon" style="background:rgba(47,190,133,.12);color:#2FBE85;"><i class="fa-solid fa-sack-dollar"></i></div>
    </div>
    <div class="stat-card">
        <div><span class="stat-value"><?= count($paidOrders) ?></span><span class="stat-label">Paid Orders</span></div>
        <div class="stat-icon" style="background:rgba(201,121,61,.12);color:#C9793D;"><i class="fa-solid fa-receipt"></i></div>
    </div>
    <div class="stat-card">
        <div><span class="stat-value"><?= money($avgOrderValue) ?></span><span class="stat-label">Avg. Order Value</span></div>
        <div class="stat-icon" style="background:rgba(227,167,59,.15);color:#E3A73B;"><i class="fa-solid fa-chart-simple"></i></div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-panel">
            <div class="admin-panel-head"><h3>Revenue — Last 30 Days</h3></div>
            <div class="admin-panel-body"><canvas id="salesChart" height="100"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-panel">
            <div class="admin-panel-head"><h3>Orders by Status</h3></div>
            <div class="admin-panel-body p-0">
                <table class="table-dtc w-100">
                    <?php foreach ($byStatus as $status => $count): ?>
                        <tr><td><span class="badge-pill <?= status_badge_class($status) ?>"><?= status_label($status) ?></span></td><td style="text-align:right;"><?= $count ?></td></tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        <div class="admin-panel">
            <div class="admin-panel-head"><h3>Revenue by Payment Method</h3></div>
            <div class="admin-panel-body p-0">
                <table class="table-dtc w-100">
                    <?php foreach ($byMethod as $method => $amount): ?>
                        <tr><td><?= status_label($method) ?></td><td style="text-align:right;" class="mono"><?= money($amount) ?></td></tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
new Chart(document.getElementById('salesChart'), {
    type: 'bar',
    data: { labels: <?= json_encode($labels) ?>, datasets: [{ label: 'Revenue', data: <?= json_encode($series) ?>, backgroundColor: '#C9793D', borderRadius: 4 }] },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } },
});
</script>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
