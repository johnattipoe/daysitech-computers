<?php
/** @var int|float $total */
$total = $total ?? 0;
?>

<div class="dtc-breadcrumb">
    <div class="container-inner"><a href="/">Home</a> / <span class="current">Your Cart</span></div>
</div>

<section class="section-sm">
    <div class="container-inner">
        <h1 class="mb-4" style="font-size:1.75rem;">Your Cart</h1>

        <?php if (empty($items)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-cart-shopping"></i>
                <h4>Your cart is empty</h4>
                <p>Browse our products and add a few to get started.</p>
                <a href="/products" class="btn btn-copper mt-3">Start Shopping</a>
            </div>
        <?php else: ?>
        <div class="row g-5">
            <div class="col-lg-8">
                <?php foreach ($items as $row): $p = $row['product']; ?>
                    <div class="cart-row">
                        <img src="<?= e($p['images'][0] ?? '') ?>" alt="<?= e($p['name']) ?>" onerror="this.src=''">
                        <div>
                            <a href="/products/<?= e($p['slug'] ?? $p['id']) ?>" style="text-decoration:none;color:#0B1F3A;font-weight:600;"><?= e($p['name']) ?></a>
                            <div class="text-muted-dtc small mono"><?= money($p['price']) ?> each</div>
                        </div>
                        <form action="/cart/update" method="POST" class="qty-cell">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= e($p['id']) ?>">
                            <div class="qty-stepper">
                                <button type="button" onclick="this.nextElementSibling.stepDown();this.form.requestSubmit();">−</button>
                                <input type="number" name="qty" value="<?= (int) $row['qty'] ?>" min="1" max="<?= (int) $p['stock'] ?>" onchange="this.form.requestSubmit()">
                                <button type="button" onclick="this.previousElementSibling.stepUp();this.form.requestSubmit();">+</button>
                            </div>
                        </form>
                        <div class="subtotal-cell mono fw-bold"><?= money($row['subtotal']) ?></div>
                        <form action="/cart/remove" method="POST" class="remove-cell">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= e($p['id']) ?>">
                            <button type="submit" class="btn btn-sm text-danger" aria-label="Remove"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                <?php endforeach; ?>
                <form action="/cart/clear" method="POST" class="mt-3" data-confirm="Clear your entire cart?">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm text-muted-dtc"><i class="fa-solid fa-trash-can me-1"></i>Clear Cart</button>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="summary-card">
                    <h6 class="mb-3">Order Summary</h6>
                    <div class="summary-line"><span>Subtotal</span><span><?= money($total) ?></span></div>
                    <div class="summary-line"><span>Shipping</span><span><?= $total >= 500 ? 'Free' : money(25) ?></span></div>
                    <div class="summary-total"><span>Total</span><span><?= money($total >= 500 ? $total : $total + 25) ?></span></div>
                    <a href="/checkout" class="btn btn-copper w-100 mt-4">Proceed to Checkout <i class="fa-solid fa-arrow-right ms-1"></i></a>
                    <a href="/products" class="btn btn-sm w-100 mt-2 text-center text-muted-dtc">Continue Shopping</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
