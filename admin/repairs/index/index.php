<?php

use App\Models\Repair;

$status = $_GET['status'] ?? '';
$repairs = $status ? Repair::byStatus($status, 200) : Repair::where([], 'created_at:desc', 200);

$title = 'Repairs';
ob_start();
?>

<div class="admin-panel">
    <div class="admin-panel-head">
        <h3>All Repair Tickets (<?= count($repairs) ?>)</h3>
        <form action="/admin/repairs" method="GET">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <?php foreach (REPAIR_STATUSES as $s): ?>
                    <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= status_label($s) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <div class="admin-panel-body p-0">
        <?php if (empty($repairs)): ?>
            <div class="empty-state"><i class="fa-solid fa-screwdriver-wrench"></i><h4>No repair tickets found</h4></div>
        <?php else: ?>
            <table class="table-dtc w-100">
                <tr><th>Ticket #</th><th>Customer</th><th>Device</th><th>Service</th><th>Priority</th><th>Status</th><th></th></tr>
                <?php foreach ($repairs as $r): ?>
                    <tr>
                        <td class="mono"><?= e($r['ticket_number']) ?></td>
                        <td><?= e($r['customer']['name'] ?? '—') ?></td>
                        <td><?= e($r['device_type']) ?> <?= e($r['brand'] ?? '') ?></td>
                        <td><?= e($r['service_type']) ?></td>
                        <td><span class="badge-pill <?= ($r['priority'] ?? 'normal') === 'urgent' ? 'badge-danger' : 'badge-neutral' ?>"><?= ucfirst($r['priority'] ?? 'normal') ?></span></td>
                        <td><span class="badge-pill <?= status_badge_class($r['status']) ?>"><?= status_label($r['status']) ?></span></td>
                        <td><a href="/admin/repairs/<?= e($r['id']) ?>" class="btn btn-sm btn-ink">Manage</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
