<?php
/** @var array<string, mixed> $order */
$order = $order ?? [];
?>

<section class="section text-center">
    <div class="container-inner" style="max-width:600px;">
        <i class="fa-solid fa-circle-check" style="font-size:3.5rem;color:#2FBE85;"></i>
        <h1 class="mt-3" style="font-size:2rem;">Order Confirmed!</h1>
        <p class="text-muted-dtc">Thanks for shopping with Daysitech Computers. We've sent a confirmation to your email.</p>

        <div class="summary-card text-start mt-4">
            <div class="summary-line"><span>Order Number</span><span class="mono fw-bold"><?= e($order['order_number']) ?></span></div>
            <div class="summary-line"><span>Payment Method</span><span><?= status_label($order['payment_method']) ?></span></div>
            <div class="summary-line"><span>Status</span><span class="badge-pill <?= status_badge_class($order['status']) ?>"><?= status_label($order['status']) ?></span></div>
            <div class="summary-total"><span>Total Paid</span><span><?= money($order['total']) ?></span></div>
        </div>
        <div class="d-flex gap-3 justify-content-center mt-4">
            <a href="/products" class="btn btn-ink">Continue Shopping</a>
            <a href="/account/orders" class="btn btn-copper">View My Orders</a>
        </div>
    </div>
</section>
