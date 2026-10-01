<?php
$title = 'Dashboard';
ob_start();
?>
<div class="card shadow-sm border-0">
    <div class="card-body">
        <h4 class="mb-3">Welcome to the Daysitech admin panel</h4>
        <p class="mb-0 text-muted">Your admin login is working properly. Use the navigation to manage products, orders, repairs, inventory, and settings.</p>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
