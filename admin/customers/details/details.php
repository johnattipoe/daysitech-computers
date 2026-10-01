<?php

use App\Models\User;
use App\Models\Order;
use App\Models\Repair;

$id = $GLOBALS['adminRouteParams'][0] ?? null;
$customer = $id ? User::find($id) : null;

if (!$customer) {
    flash('error', 'Customer not found.');
    redirect('/admin/customers');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $newStatus = ($customer['status'] ?? 'active') === 'active' ? 'suspended' : 'active';
    User::update($id, ['status' => $newStatus]);
    flash('success', 'Customer status updated.');
    redirect('/admin/customers/' . $id);
}

$orders = Order::forUser($id);
$repairs = Repair::forUser($id);

$title = 'Customer Profile';
ob_start();
?>

<a href="/admin/customers" class="small text-muted-dtc mb-3 d-inline-block"><i class="fa-solid fa-arrow-left me-1"></i>Back to Customers</a>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="admin-panel"><div class="admin-panel-body text-center">
            <i class="fa-solid fa-circle-user" style="font-size:3.5rem;color:#0B1F3A;"></i>
            <h5 class="mt-2 mb-0"><?= e($customer['name']) ?></h5>
            <span class="text-muted-dtc small"><?= e($customer['email']) ?></span>
            <hr>
            <div class="text-start">
                <p class="mb-1"><i class="fa-solid fa-phone me-2"></i><?= e($customer['phone'] ?? '—') ?></p>
                <p class="mb-1"><i class="fa-solid fa-location-dot me-2"></i><?= e($customer['address'] ?? '—') ?>, <?= e($customer['city'] ?? '') ?></p>
                <p class="mb-0"><i class="fa-solid fa-calendar me-2"></i>Joined <?= format_date($customer['created_at']) ?></p>
            </div>
            <form action="/admin/customers/<?= e($id) ?>" method="POST" class="mt-4" data-confirm="<?= ($customer['status'] ?? 'active') === 'active' ? 'Suspend this customer account?' : 'Reactivate this customer account?' ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn <?= ($customer['status'] ?? 'active') === 'active' ? 'text-danger' : 'btn-copper' ?> w-100">
                    <?= ($customer['status'] ?? 'active') === 'active' ? 'Suspend Account' : 'Reactivate Account' ?>
                </button>
            </form>
        </div></div>
    </div>

    <div class="col-lg-8">
        <div class="admin-panel">
            <div class="admin-panel-head"><h3>Order History (<?= count($orders) ?>)</h3></div>
            <div class="admin-panel-body p-0">
                <?php if (empty($orders)): ?>
                    <div class="empty-state"><i class="fa-solid fa-box-open"></i><h4>No orders yet</h4></div>
                <?php else: ?>
                    <table class="table-dtc w-100">
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td class="mono"><a href="/admin/orders/<?= e($o['id']) ?>"><?= e($o['order_number']) ?></a></td>
                                <td><?= money($o['total']) ?></td>
                                <td><span class="badge-pill <?= status_badge_class($o['status']) ?>"><?= status_label($o['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head"><h3>Repair History (<?= count($repairs) ?>)</h3></div>
            <div class="admin-panel-body p-0">
                <?php if (empty($repairs)): ?>
                    <div class="empty-state"><i class="fa-solid fa-screwdriver-wrench"></i><h4>No repairs yet</h4></div>
                <?php else: ?>
                    <table class="table-dtc w-100">
                        <?php foreach ($repairs as $r): ?>
                            <tr>
                                <td class="mono"><a href="/admin/repairs/<?= e($r['id']) ?>"><?= e($r['ticket_number']) ?></a></td>
                                <td><?= e($r['device_type']) ?></td>
                                <td><span class="badge-pill <?= status_badge_class($r['status']) ?>"><?= status_label($r['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
