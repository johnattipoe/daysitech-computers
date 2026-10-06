<?php $cartCount = \App\Models\Cart::count(); ?>
<div class="dtc-topbar">
    <div class="container-inner">
        <div class="topbar-links">
            <a href="tel:<?= e(config('app.business.phone')) ?>"><i class="fa-solid fa-phone"></i><?= e(config('app.business.phone')) ?></a>
            <a href="mailto:<?= e(config('app.business.email')) ?>"><i class="fa-solid fa-envelope"></i><?= e(config('app.business.email')) ?></a>
            <span class="d-none d-md-inline"><i class="fa-solid fa-clock"></i><?= e(config('app.business.hours')) ?></span>
        </div>
        <div class="topbar-links">
            <a href="/repairs/track"><i class="fa-solid fa-magnifying-glass"></i>Track Repair</a>
            <?php if (!is_logged_in()): ?>
                <a href="/login">Sign In</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<header class="dtc-navbar">
    <div class="container-inner navbar-inner">
        <a href="/" class="brand">
            <span class="brand-mark">DT</span>
            <span class="brand-text">Daysitech<small>Computers &middot; Sales &amp; Repairs</small></span>
        </a>

        <ul class="nav-links">
            <li><a href="/" class="<?= ($_SERVER['REQUEST_URI'] ?? '') === '/' ? 'active' : '' ?>">Home</a></li>
            <li><a href="/products" class="<?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/products') ? 'active' : '' ?>">Shop</a></li>
            <li><a href="/categories">Categories</a></li>
            <li><a href="/repairs/book" class="<?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/repairs') ? 'active' : '' ?>">Book a Repair</a></li>
            <li><a href="/about">About</a></li>
            <li><a href="/contact">Contact</a></li>
        </ul>

        <form class="nav-search" action="/products" method="GET">
            <input type="search" name="q" placeholder="Search laptops, parts, brands…" value="<?= e($_GET['q'] ?? '') ?>">
            <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>

        <div class="nav-actions">
            <a href="/cart" aria-label="Cart">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="cart-count" style="display:<?= $cartCount ? 'flex' : 'none' ?>"><?= $cartCount ?></span>
            </a>
            <?php if (is_logged_in()): ?>
                <div class="dropdown">
                    <a href="#" role="button" data-bs-toggle="dropdown" aria-label="Account"><i class="fa-solid fa-circle-user"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text text-muted small">Hi, <?= e(current_user()['name']) ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/account/dashboard"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a></li>
                        <li><a class="dropdown-item" href="/account/orders"><i class="fa-solid fa-box me-2"></i>My Orders</a></li>
                        <li><a class="dropdown-item" href="/account/repairs"><i class="fa-solid fa-screwdriver-wrench me-2"></i>My Repairs</a></li>
                        <li><a class="dropdown-item" href="/account/wishlist"><i class="fa-solid fa-heart me-2"></i>Wishlist</a></li>
                        <li><a class="dropdown-item" href="/account/profile"><i class="fa-solid fa-user me-2"></i>Profile</a></li>
                        <?php if (is_admin()): ?>
                            <li><a class="dropdown-item" href="/admin/dashboard"><i class="fa-solid fa-shield-halved me-2"></i>Admin Panel</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="/logout"><i class="fa-solid fa-right-from-bracket me-2"></i>Sign Out</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="/login" aria-label="Sign in"><i class="fa-solid fa-circle-user"></i></a>
            <?php endif; ?>
            <button class="nav-toggle" type="button" aria-label="Menu"><i class="fa-solid fa-bars"></i></button>
        </div>
    </div>
</header>

<!-- Mobile off-canvas menu -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form class="mb-4" action="/products" method="GET">
            <input type="search" name="q" class="form-control" placeholder="Search products…">
        </form>
        <ul class="list-unstyled d-grid gap-3">
            <li><a href="/">Home</a></li>
            <li><a href="/products">Shop</a></li>
            <li><a href="/categories">Categories</a></li>
            <li><a href="/repairs/book">Book a Repair</a></li>
            <li><a href="/repairs/track">Track Repair</a></li>
            <li><a href="/about">About</a></li>
            <li><a href="/contact">Contact</a></li>
            <?php if (!is_logged_in()): ?>
                <li><a href="/login">Sign In</a></li>
                <li><a href="/register">Create Account</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>
