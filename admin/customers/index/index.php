<?php

use App\Models\User;

$customers = User::customers();
$title = 'Customers';
ob_start();
?>

<div class="admin-panel">
    <div class="admin-panel-head d-flex justify-content-between align-items-center">
        <h3>Customers</h3>
        <span class="text-muted-dtc small"><?= count($customers) ?> customers</span>
    </div>
    <div class="admin-panel-body p-0">
        <?php if (empty($customers)): ?>
            <div class="empty-state"><i class="fa-solid fa-users"></i><h4>No customers yet</h4></div>
        <?php else: ?>
            <table class="table-dtc w-100">
                <thead>
                    <tr><th>Customer</th><th>Phone</th><th>Status</th><th>Joined</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><strong><?= e($customer['name'] ?? '') ?></strong><br><span class="text-muted-dtc small"><?= e($customer['email'] ?? '') ?></span></td>
                            <td><?= e($customer['phone'] ?? '—') ?></td>
                            <td><span class="badge-pill <?= ($customer['status'] ?? 'active') === 'active' ? 'badge-success' : 'badge-danger' ?>"><?= e(ucfirst($customer['status'] ?? 'active')) ?></span></td>
                            <td><?= format_date($customer['created_at'] ?? null) ?></td>
                            <td><a href="/admin/customers/<?= e($customer['id']) ?>" class="btn btn-sm btn-outline-secondary">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
