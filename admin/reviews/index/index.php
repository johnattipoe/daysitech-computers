<?php

use App\Models\Review;

$pending = Review::pending();
$all = Review::where([], 'created_at:desc', 200);

$title = 'Reviews';
ob_start();
?>

<div class="admin-panel">
    <div class="admin-panel-head"><h3>Pending Approval (<?= count($pending) ?>)</h3></div>
    <div class="admin-panel-body p-0">
        <?php if (empty($pending)): ?>
            <div class="empty-state"><i class="fa-solid fa-check"></i><h4>No reviews awaiting approval</h4></div>
        <?php else: ?>
            <table class="table-dtc w-100">
                <tr><th>Customer</th><th>Rating</th><th>Comment</th><th></th></tr>
                <?php foreach ($pending as $r): ?>
                    <tr>
                        <td><?= e($r['customer_name']) ?></td>
                        <td><?= star_rating_html($r['rating']) ?></td>
                        <td><?= e(truncate($r['comment'], 80)) ?></td>
                        <td class="d-flex gap-2">
                            <form action="/admin/reviews/<?= e($r['id']) ?>/approve" method="POST">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-copper"><i class="fa-solid fa-check"></i></button>
                            </form>
                            <form action="/admin/reviews/<?= e($r['id']) ?>/delete" method="POST" data-confirm="Delete this review?">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm text-danger"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel-head"><h3>All Reviews</h3></div>
    <div class="admin-panel-body p-0">
        <table class="table-dtc w-100">
            <tr><th>Customer</th><th>Rating</th><th>Comment</th><th>Status</th><th></th></tr>
            <?php foreach ($all as $r): ?>
                <tr>
                    <td><?= e($r['customer_name']) ?></td>
                    <td><?= star_rating_html($r['rating']) ?></td>
                    <td><?= e(truncate($r['comment'], 80)) ?></td>
                    <td><span class="badge-pill <?= !empty($r['is_approved']) ? 'badge-success' : 'badge-warning' ?>"><?= !empty($r['is_approved']) ? 'Approved' : 'Pending' ?></span></td>
                    <td>
                        <form action="/admin/reviews/<?= e($r['id']) ?>/delete" method="POST" data-confirm="Delete this review?">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm text-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
