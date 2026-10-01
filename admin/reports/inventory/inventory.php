<?php

use App\Models\Product;
use App\Models\Category;

$products = Product::all(500);
$categories = Category::active();
$categoryMap = array_column($categories, 'name', 'id');

$byCategory = [];
foreach ($products as $p) {
    $catName = $categoryMap[$p['category_id'] ?? ''] ?? 'Uncategorized';
    $byCategory[$catName] = ($byCategory[$catName] ?? 0) + (int) $p['stock'];
}

usort($products, fn($a, $b) => (int) $a['stock'] <=> (int) $b['stock']);

$title = 'Inventory Report';
ob_start();
?>

<div class="d-flex gap-2 mb-4">
    <a href="/admin/reports/sales" class="btn btn-ink btn-sm">Sales</a>
    <a href="/admin/reports/inventory" class="btn btn-copper btn-sm">Inventory</a>
    <a href="/admin/reports/repairs" class="btn btn-ink btn-sm">Repairs</a>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="admin-panel">
            <div class="admin-panel-head"><h3>Units by Category</h3></div>
            <div class="admin-panel-body"><canvas id="catChart" height="220"></canvas></div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="admin-panel">
            <div class="admin-panel-head"><h3>Stock Levels (Lowest First)</h3></div>
            <div class="admin-panel-body p-0">
                <table class="table-dtc w-100">
                    <tr><th>Product</th><th>Category</th><th>Stock</th><th>Value</th></tr>
                    <?php foreach (array_slice($products, 0, 30) as $p): ?>
                        <tr>
                            <td><?= e($p['name']) ?></td>
                            <td><?= e($categoryMap[$p['category_id'] ?? ''] ?? '—') ?></td>
                            <td><span class="badge-pill <?= (int) $p['stock'] === 0 ? 'badge-danger' : ((int) $p['stock'] <= LOW_STOCK_THRESHOLD ? 'badge-warning' : 'badge-success') ?>"><?= (int) $p['stock'] ?></span></td>
                            <td class="mono"><?= money((float) $p['price'] * (int) $p['stock']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
new Chart(document.getElementById('catChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_keys($byCategory)) ?>,
        datasets: [{ data: <?= json_encode(array_values($byCategory)) ?>, backgroundColor: ['#0B1F3A','#C9793D','#2FBE85','#E3A73B','#4B586E','#1FA8DC'] }],
    },
});
</script>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
