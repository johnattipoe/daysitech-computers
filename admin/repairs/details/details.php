<?php

use App\Models\Repair;

$id = $GLOBALS['adminRouteParams'][0] ?? null;
$repair = $id ? Repair::find($id) : null;

if (!$repair) {
    flash('error', 'Repair ticket not found.');
    redirect('/admin/repairs');
}

$title = 'Repair ' . $repair['ticket_number'];
ob_start();
?>

<a href="/admin/repairs" class="small text-muted-dtc mb-3 d-inline-block"><i class="fa-solid fa-arrow-left me-1"></i>Back to Repairs</a>
<button type="button" class="admin-copy-button" data-copy-value="<?= e($repair['ticket_number']) ?>"><i class="fa-regular fa-copy" aria-hidden="true"></i> Copy ticket number</button>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-panel"><div class="admin-panel-body">
            <h6 class="mb-3">Device & Issue</h6>
            <div class="summary-line"><span>Device</span><span><?= e($repair['device_type']) ?> — <?= e($repair['brand']) ?> <?= e($repair['model']) ?></span></div>
            <div class="summary-line"><span>Serial Number</span><span class="mono"><?= e($repair['serial_number'] ?: '—') ?></span></div>
            <div class="summary-line"><span>Service Requested</span><span><?= e($repair['service_type']) ?></span></div>
            <div class="summary-line"><span>Drop-off Date</span><span><?= format_date($repair['drop_off_date']) ?></span></div>
            <div class="mt-3">
                <span class="form-label d-block">Issue Description</span>
                <p class="text-muted-dtc"><?= e($repair['issue_description']) ?></p>
            </div>
        </div></div>

        <div class="admin-panel"><div class="admin-panel-body">
            <h6 class="mb-3">Customer</h6>
            <p class="mb-1"><strong><?= e($repair['customer']['name'] ?? '') ?></strong></p>
            <p class="mb-1 text-muted-dtc small"><i class="fa-solid fa-phone me-1"></i><?= e($repair['customer']['phone'] ?? '') ?></p>
            <p class="mb-0 text-muted-dtc small"><i class="fa-solid fa-envelope me-1"></i><?= e($repair['customer']['email'] ?? '') ?></p>
        </div></div>
    </div>

    <div class="col-lg-4">
        <div class="admin-panel"><div class="admin-panel-body">
            <h6 class="mb-3">Update Ticket</h6>
            <form action="/admin/repairs/<?= e($id) ?>/update" method="POST" data-unsaved-warning>
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach (REPAIR_STATUSES as $s): ?>
                            <option value="<?= $s ?>" <?= $repair['status'] === $s ? 'selected' : '' ?>><?= status_label($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Estimated Cost (<?= config('app.currency_symbol') ?>)</label>
                    <input type="number" step="0.01" name="estimated_cost" class="form-control" value="<?= e($repair['estimated_cost'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Technician Notes</label>
                    <textarea name="technician_notes" class="form-control" rows="4"><?= e($repair['technician_notes'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-copper w-100">Save Update</button>
            </form>
        </div></div>
    </div>
</div>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
