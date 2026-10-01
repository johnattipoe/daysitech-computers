<?php

use App\Models\Payment;

$payments = Payment::recent(200);

$title = 'Payments';
ob_start();
?>

<div class="admin-panel">
    <div class="admin-panel-head"><h3>All Payments (<?= count($payments) ?>)</h3></div>
    <div class="admin-panel-body p-0">
        <?php if (empty($payments)): ?>
            <div class="empty-state"><i class="fa-solid fa-credit-card"></i><h4>No payments recorded yet</h4></div>
        <?php else: ?>
            <table class="table-dtc w-100">
                <tr><th>Reference</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr>
                <?php foreach ($payments as $p): ?>
                    <tr>
                        <td class="mono"><?= e($p['provider_reference'] ?? '—') ?></td>
                        <td class="mono"><?= money($p['amount']) ?></td>
                        <td><?= status_label($p['method']) ?></td>
                        <td><span class="badge-pill <?= $p['status'] === 'success' ? 'badge-success' : ($p['status'] === 'failed' ? 'badge-danger' : 'badge-warning') ?>"><?= status_label($p['status']) ?></span></td>
                        <td><?= format_datetime($p['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
