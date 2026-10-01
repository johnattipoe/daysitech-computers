<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? e($title) . ' — Admin — ' . config('app.business.name') : 'Admin — ' . config('app.business.name') ?></title>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <link rel="icon" href="<?= asset('images/logo/favicon.png') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.5/sweetalert2.min.css" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/admin.css') ?>" rel="stylesheet">
</head>
<body>
<div class="dtc-page-loader" id="pageLoader" role="status" aria-live="polite" aria-label="Loading Daysitech Computers">
    <div class="dtc-loader-grid" aria-hidden="true"></div>
    <div class="dtc-loader-panel">
        <div class="dtc-loader-topline"><span>DT / ADMIN BOOT</span><span class="dtc-loader-status"><i></i> LIVE</span></div>
        <div class="dtc-loader-brand"><span class="dtc-loader-mark"><b>D</b><b>T</b></span><span>Daysitech<br><em>Control Room</em></span></div>
        <div class="dtc-loader-line"><span></span></div>
        <p>Opening your control room<span class="dtc-loader-dots" aria-hidden="true">...</span></p>
        <div class="dtc-loader-meta"><span>ACCESS / ADMIN</span><span>ACCRA / GH</span></div>
    </div>
</div>
<div class="admin-shell">
    <aside class="admin-sidebar">
        <a href="/admin/dashboard" class="sidebar-brand">
            <span class="brand-mark">DT</span>
            <span>Daysitech <small style="display:block;font-weight:400;opacity:.6;">Admin Panel</small></span>
        </a>

        <?php
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        $isActive = fn(string $prefix) => str_starts_with($path, $prefix) ? 'active' : '';
        ?>

        <div class="nav-section-title">Overview</div>
        <a href="/admin/dashboard" class="nav-link <?= $isActive('/admin/dashboard') ?>"><i class="fa-solid fa-gauge"></i>Dashboard</a>

        <div class="nav-section-title">Catalog</div>
        <a href="/admin/products" class="nav-link <?= $isActive('/admin/products') ?>"><i class="fa-solid fa-laptop"></i>Products</a>
        <a href="/admin/products/categories" class="nav-link <?= str_contains($path, 'categories') ? 'active' : '' ?>"><i class="fa-solid fa-tags"></i>Categories &amp; Brands</a>
        <a href="/admin/inventory" class="nav-link <?= $isActive('/admin/inventory') ?>"><i class="fa-solid fa-boxes-stacked"></i>Inventory</a>

        <div class="nav-section-title">Sales</div>
        <a href="/admin/orders" class="nav-link <?= $isActive('/admin/orders') ?>"><i class="fa-solid fa-cart-shopping"></i>Orders</a>
        <a href="/admin/payments" class="nav-link <?= $isActive('/admin/payments') ?>"><i class="fa-solid fa-credit-card"></i>Payments</a>

        <div class="nav-section-title">Service</div>
        <a href="/admin/repairs" class="nav-link <?= $isActive('/admin/repairs') ?>"><i class="fa-solid fa-screwdriver-wrench"></i>Repairs</a>

        <div class="nav-section-title">People</div>
        <a href="/admin/customers" class="nav-link <?= $isActive('/admin/customers') ?>"><i class="fa-solid fa-users"></i>Customers</a>
        <a href="/admin/staff" class="nav-link <?= $isActive('/admin/staff') ?>"><i class="fa-solid fa-user-tie"></i>Staff</a>
        <a href="/admin/reviews" class="nav-link <?= $isActive('/admin/reviews') ?>"><i class="fa-solid fa-star"></i>Reviews</a>

        <div class="nav-section-title">Insights</div>
        <a href="/admin/reports/sales" class="nav-link <?= $isActive('/admin/reports') ?>"><i class="fa-solid fa-chart-line"></i>Reports</a>
        <a href="/admin/settings" class="nav-link <?= $isActive('/admin/settings') ?>"><i class="fa-solid fa-gear"></i>Settings</a>

        <div class="nav-section-title">&nbsp;</div>
        <a href="/" class="nav-link"><i class="fa-solid fa-arrow-left"></i>Back to Store</a>
        <a href="/logout" class="nav-link"><i class="fa-solid fa-right-from-bracket"></i>Sign Out</a>
    </aside>

    <div class="admin-main">
        <div class="admin-topbar">
            <h1><?= e($title ?? 'Admin') ?></h1>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted-dtc small"><?= e(current_user()['name'] ?? '') ?> &middot; <span class="badge-pill badge-info"><?= e(ucfirst(user_role())) ?></span></span>
                <i class="fa-solid fa-circle-user" style="font-size:1.5rem;color:#0B1F3A;"></i>
            </div>
        </div>
        <div class="admin-content">
            <?php $flashSuccess = flash('success'); $flashError = flash('error'); ?>
            <?php if ($flashSuccess): ?>
                <div class="dtc-alert dtc-alert-success" data-flash="success" data-message="<?= e($flashSuccess) ?>" style="display:none"><i class="fa-solid fa-circle-check"></i> <?= e($flashSuccess) ?></div>
            <?php endif; ?>
            <?php if ($flashError): ?>
                <div class="dtc-alert dtc-alert-error" data-flash="error" data-message="<?= e($flashError) ?>" style="display:none"><i class="fa-solid fa-circle-exclamation"></i> <?= e($flashError) ?></div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
<script src="<?= asset('js/app.js') ?>"></script>
<script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
