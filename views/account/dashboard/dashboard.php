<?php
/** @var array<int, array<string, mixed>> $orders */
/** @var array<int, array<string, mixed>> $repairs */
$orders = $orders ?? [];
$repairs = $repairs ?? [];
?>

<div class="dtc-breadcrumb">
    <div class="container-inner"><a href="/">Home</a> / <span class="current">My Dashboard</span></div>
</div>

<section class="section-sm">
    <div class="container-inner">
        <div class="row g-4">
            <div class="col-lg-3"><?php require base_path('views/account/_sidebar.php'); ?></div>
            <div class="col-lg-9">
                <h1 class="mb-4" style="font-size:1.5rem;">Welcome back, <?= e(explode(' ', current_user()['name'])[0]) ?> 👋</h1>

                <div class="stat-grid">
                    <div class="stat-card">
                        <div><span class="stat-value"><?= count($orders) ?></span><span class="stat-label">Recent Orders</span></div>
                        <div class="stat-icon" style="background:rgba(47,190,133,.12);color:#2FBE85;"><i class="fa-solid fa-box"></i></div>
                    </div>
                    <div class="stat-card">
                        <div><span class="stat-value"><?= count($repairs) ?></span><span class="stat-label">Active Repairs</span></div>
                        <div class="stat-icon" style="background:rgba(201,121,61,.12);color:#C9793D;"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                    </div>
                </div>

                <div class="admin-panel">
                    <div class="admin-panel-head"><h3>Recent Orders</h3><a href="/account/orders" class="small">View all</a></div>
                    <div class="admin-panel-body">
                        <?php if (empty($orders)): ?>
                            <p class="text-muted-dtc mb-0">No orders yet. <a href="/products">Start shopping →</a></p>
                        <?php else: ?>
                            <table class="table-dtc w-100">
                                <tr><th>Order #</th><th>Date</th><th>Total</th><th>Status</th></tr>
                                <?php foreach ($orders as $o): ?>
                                    <tr>
                                        <td class="mono"><a href="/account/orders/<?= e($o['id']) ?>"><?= e($o['order_number']) ?></a></td>
                                        <td><?= format_date($o['created_at']) ?></td>
                                        <td><?= money($o['total']) ?></td>
                                        <td><span class="badge-pill <?= status_badge_class($o['status']) ?>"><?= status_label($o['status']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="admin-panel">
                    <div class="admin-panel-head"><h3>Recent Repairs</h3><a href="/account/repairs" class="small">View all</a></div>
                    <div class="admin-panel-body">
                        <?php if (empty($repairs)): ?>
                            <p class="text-muted-dtc mb-0">No repairs booked yet. <a href="/repairs/book">Book one →</a></p>
                        <?php else: ?>
                            <table class="table-dtc w-100">
                                <tr><th>Ticket #</th><th>Device</th><th>Status</th></tr>
                                <?php foreach ($repairs as $r): ?>
                                    <tr>
                                        <td class="mono"><a href="/repairs/track?ticket=<?= e($r['ticket_number']) ?>"><?= e($r['ticket_number']) ?></a></td>
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
    </div>
</section>
