<div class="dtc-breadcrumb">
    <div class="container-inner"><a href="/">Home</a> / <a href="/account/dashboard">Dashboard</a> / <span class="current">My Orders</span></div>
</div>

<section class="section-sm">
    <div class="container-inner">
        <div class="row g-4">
            <div class="col-lg-3"><?php require base_path('views/account/_sidebar.php'); ?></div>
            <div class="col-lg-9">

                <?php if (!empty($single) && !empty($order)): ?>
                    <a href="/account/orders" class="small text-muted-dtc mb-3 d-inline-block"><i class="fa-solid fa-arrow-left me-1"></i>Back to Orders</a>
                    <div class="summary-card mb-4">
                        <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
                            <h5 class="mb-0">Order <?= e($order['order_number']) ?></h5>
                            <span class="badge-pill <?= status_badge_class($order['status']) ?>"><?= status_label($order['status']) ?></span>
                        </div>
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="summary-line"><span><?= e($item['name']) ?> × <?= $item['qty'] ?></span><span class="mono"><?= money($item['subtotal']) ?></span></div>
                        <?php endforeach; ?>
                        <div class="summary-line"><span>Shipping</span><span><?= money($order['shipping_fee']) ?></span></div>
                        <div class="summary-total"><span>Total</span><span><?= money($order['total']) ?></span></div>

                        <?php if (!in_array($order['status'], [ORDER_STATUS_DELIVERED, ORDER_STATUS_CANCELLED], true)): ?>
                            <form action="/account/orders/<?= e($order['id']) ?>/cancel" method="POST" class="mt-4" data-confirm="Cancel this order?">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm text-danger">Cancel Order</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <h1 class="mb-4" style="font-size:1.5rem;">My Orders</h1>
                    <?php if (empty($orders)): ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-box-open"></i>
                            <h4>No orders yet</h4>
                            <p>Your placed orders will show up here.</p>
                            <a href="/products" class="btn btn-copper mt-3">Start Shopping</a>
                        </div>
                    <?php else: ?>
                        <div class="admin-panel">
                            <div class="admin-panel-body p-0">
                                <table class="table-dtc w-100">
                                    <tr><th>Order #</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th><th></th></tr>
                                    <?php foreach ($orders as $o): ?>
                                        <tr>
                                            <td class="mono"><?= e($o['order_number']) ?></td>
                                            <td><?= format_date($o['created_at']) ?></td>
                                            <td><?= count($o['items']) ?> item(s)</td>
                                            <td><?= money($o['total']) ?></td>
                                            <td><span class="badge-pill <?= status_badge_class($o['status']) ?>"><?= status_label($o['status']) ?></span></td>
                                            <td><a href="/account/orders/<?= e($o['id']) ?>" class="btn btn-sm btn-ink">View</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
