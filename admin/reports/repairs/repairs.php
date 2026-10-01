<?php

use App\Models\Repair;

$repairs = Repair::where([], 'created_at:desc', 1000);

$byStatus = [];
foreach ($repairs as $r) {
    $byStatus[$r['status']] = ($byStatus[$r['status']] ?? 0) + 1;
}

$byService = [];
foreach ($repairs as $r) {
    $s = $r['service_type'] ?? 'Other';
    $byService[$s] = ($byService[$s] ?? 0) + 1;
}
arsort($byService);

$completed = array_filter($repairs, fn($r) => $r['status'] === REPAIR_STATUS_COMPLETED);
$totalCompletedValue = array_sum(array_map(fn($r) => (float) ($r['estimated_cost'] ?? 0), $completed));

$title = 'Repairs Report';
ob_start();
?>

<div class="d-flex gap-2 mb-4">
    <a href="/admin/reports/sales" class="btn btn-ink btn-sm">Sales</a>
    <a href="/admin/reports/inventory" class="btn btn-ink btn-sm">Inventory</a>
    <a href="/admin/reports/repairs" class="btn btn-copper btn-sm">Repairs</a>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div><span class="stat-value"><?= count($repairs) ?></span><span class="stat-label">Total Tickets</span></div>
        <div class="stat-icon" style="background:rgba(11,31,58,.1);color:#0B1F3A;"><i class="fa-solid fa-ticket"></i></div>
    </div>
    <div class="stat-card">
        <div><span class="stat-value"><?= count($completed) ?></span><span class="stat-label">Completed</span></div>
        <div class="stat-icon" style="background:rgba(47,190,133,.12);color:#2FBE85;"><i class="fa-solid fa-circle-check"></i></div>
    </div>
    <div class="stat-card">
        <div><span class="stat-value"><?= money($totalCompletedValue) ?></span><span class="stat-label">Est. Revenue (Completed)</span></div>
        <div class="stat-icon" style="background:rgba(201,121,61,.12);color:#C9793D;"><i class="fa-solid fa-sack-dollar"></i></div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="admin-panel">
            <div class="admin-panel-head"><h3>Tickets by Status</h3></div>
            <div class="admin-panel-body"><canvas id="statusChart" height="220"></canvas></div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="admin-panel">
            <div class="admin-panel-head"><h3>Most Requested Services</h3></div>
            <div class="admin-panel-body p-0">
                <table class="table-dtc w-100">
                    <tr><th>Service Type</th><th>Requests</th></tr>
                    <?php foreach ($byService as $service => $count): ?>
                        <tr><td><?= e($service) ?></td><td style="text-align:right;"><?= $count ?></td></tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
new Chart(document.getElementById('statusChart'), {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_map('status_label', array_keys($byStatus))) ?>,
        datasets: [{ data: <?= json_encode(array_values($byStatus)) ?>, backgroundColor: ['#0B1F3A','#C9793D','#2FBE85','#E3A73B','#4B586E','#1FA8DC','#E0542C','#7B61FF','#DCE2EC'] }],
    },
});
</script>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
