<?php $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH); ?>
<div class="summary-card">
    <div class="text-center mb-3">
        <i class="fa-solid fa-circle-user" style="font-size:3rem;color:#0B1F3A;"></i>
        <h6 class="mt-2 mb-0"><?= e(current_user()['name']) ?></h6>
        <span class="text-muted-dtc small"><?= e(current_user()['email']) ?></span>
    </div>
    <hr>
    <div class="d-grid gap-1">
        <a href="/account/dashboard" class="btn btn-sm text-start <?= $path === '/account/dashboard' ? 'btn-ink' : '' ?>"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a>
        <a href="/account/orders" class="btn btn-sm text-start <?= $path === '/account/orders' ? 'btn-ink' : '' ?>"><i class="fa-solid fa-box me-2"></i>My Orders</a>
        <a href="/account/repairs" class="btn btn-sm text-start <?= $path === '/account/repairs' ? 'btn-ink' : '' ?>"><i class="fa-solid fa-screwdriver-wrench me-2"></i>My Repairs</a>
        <a href="/account/wishlist" class="btn btn-sm text-start <?= $path === '/account/wishlist' ? 'btn-ink' : '' ?>"><i class="fa-solid fa-heart me-2"></i>Wishlist</a>
        <a href="/account/profile" class="btn btn-sm text-start <?= $path === '/account/profile' ? 'btn-ink' : '' ?>"><i class="fa-solid fa-user me-2"></i>Profile</a>
        <a href="/logout" class="btn btn-sm text-start text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i>Sign Out</a>
    </div>
</div>
