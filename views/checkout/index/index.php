<?php
/** @var array<int, array<string, mixed>> $items */
/** @var int|float $total */
$items = $items ?? [];
$total = $total ?? 0;
?>

<div class="dtc-breadcrumb">
    <div class="container-inner"><a href="/">Home</a> / <a href="/cart">Cart</a> / <span class="current">Checkout</span></div>
</div>

<section class="section-sm">
    <div class="container-inner">
        <h1 class="mb-4" style="font-size:1.75rem;">Checkout</h1>

        <form action="/checkout" method="POST">
            <?= csrf_field() ?>
            <div class="row g-5">
                <div class="col-lg-7">
                    <div class="summary-card mb-4">
                        <h6 class="mb-3">Delivery Details</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?= e($user['name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= e($user['email'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" placeholder="e.g. Accra" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Delivery Address</label>
                                <textarea name="address" class="form-control" rows="2" required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Order Notes (optional)</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Delivery instructions, preferred time, etc."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="summary-card">
                        <h6 class="mb-3">Payment Method</h6>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="cash_on_delivery" checked>
                            <i class="fa-solid fa-money-bill-wave"></i>
                            <div><strong>Cash on Delivery</strong><div class="text-muted-dtc small">Pay when your order arrives</div></div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="mobile_money">
                            <i class="fa-solid fa-mobile-screen"></i>
                            <div><strong>Mobile Money</strong><div class="text-muted-dtc small">MTN, Vodafone, AirtelTigo via Paystack</div></div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="card">
                            <i class="fa-regular fa-credit-card"></i>
                            <div><strong>Card Payment</strong><div class="text-muted-dtc small">Visa / Mastercard via Paystack</div></div>
                        </label>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="summary-card">
                        <h6 class="mb-3">Your Order</h6>
                        <?php foreach ($items as $row): ?>
                            <div class="summary-line">
                                <span><?= e($row['product']['name']) ?> × <?= $row['qty'] ?></span>
                                <span class="mono"><?= money($row['subtotal']) ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="summary-line"><span>Shipping</span><span><?= $total >= 500 ? 'Free' : money(25) ?></span></div>
                        <div class="summary-total"><span>Total</span><span><?= money($total >= 500 ? $total : $total + 25) ?></span></div>
                        <button type="submit" class="btn btn-copper w-100 mt-4">Place Order <i class="fa-solid fa-lock ms-1"></i></button>
                        <p class="text-muted-dtc small text-center mt-2 mb-0"><i class="fa-solid fa-shield-halved me-1"></i>Secure checkout</p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
