<?php
$title = 'Dashboard';
ob_start();
?>
<div class="row g-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Orders</div>
                <h3 class="mt-2 mb-0">0</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Revenue</div>
                <h3 class="mt-2 mb-0">GHS 0.00</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Repairs</div>
                <h3 class="mt-2 mb-0">0</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Customers</div>
                <h3 class="mt-2 mb-0">0</h3>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mt-4">
    <div class="card-body">
        <h4 class="mb-3">Welcome back</h4>
        <p class="mb-0 text-muted">
            Your admin account is active and authenticated. The dashboard is ready for your store metrics and operations.
        </p>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
