<?php

use App\Services\FirebaseService;

/**
 * Business settings are stored in a single Firestore document
 * (collection "settings", doc "business") so they can be edited without
 * redeploying code. To have the storefront read these live instead of the
 * config/app.php defaults, swap config('app.business.*') calls for a small
 * SettingsService that checks Firestore first — left as a easy follow-up.
 */
$firebase = new FirebaseService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $data = [
        'name'    => $_POST['name'],
        'phone'   => $_POST['phone'],
        'whatsapp'=> $_POST['whatsapp'],
        'email'   => $_POST['email'],
        'address' => $_POST['address'],
        'hours'   => $_POST['hours'],
    ];

    $existing = $firebase->get('settings', 'business');
    $existing ? $firebase->update('settings', 'business', $data) : $firebase->create('settings', $data, 'business');

    flash('success', 'Settings saved.');
    redirect('/admin/settings');
}

$saved = $firebase->get('settings', 'business') ?? [];
$biz = array_merge(config('app.business'), $saved);

$title = 'Settings';
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <form action="/admin/settings" method="POST" class="admin-panel" data-unsaved-warning>
            <div class="admin-panel-body">
                <h6 class="mb-3">Business Information</h6>
                <div class="mb-3"><label class="form-label">Business Name</label><input type="text" name="name" class="form-control" value="<?= e($biz['name']) ?>" required></div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="<?= e($biz['phone']) ?>"></div>
                    <div class="col-md-6"><label class="form-label">WhatsApp</label><input type="text" name="whatsapp" class="form-control" value="<?= e($biz['whatsapp']) ?>"></div>
                </div>
                <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($biz['email']) ?>"></div>
                <div class="mb-3"><label class="form-label">Address</label><input type="text" name="address" class="form-control" value="<?= e($biz['address']) ?>"></div>
                <div class="mb-4"><label class="form-label">Business Hours</label><input type="text" name="hours" class="form-control" value="<?= e($biz['hours']) ?>"></div>
                <button type="submit" class="btn btn-copper w-100">Save Settings</button>
            </div>
        </form>

        <div class="admin-panel"><div class="admin-panel-body">
            <h6 class="mb-2">Environment</h6>
            <p class="text-muted-dtc small mb-1">Firebase Project: <span class="mono"><?= e(config('firebase.project_id') ?: 'not configured') ?></span></p>
            <p class="text-muted-dtc small mb-1">Currency: <span class="mono"><?= e(config('app.currency')) ?></span></p>
            <p class="text-muted-dtc small mb-0">Environment overrides live in <span class="mono">.env</span> — payment keys, mail credentials, and Firebase config are not editable from this panel for security.</p>
        </div></div>
    </div>
</div>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
