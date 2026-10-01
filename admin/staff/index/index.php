<?php

use App\Models\User;
use App\Services\FirebaseService;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    if (user_role() !== 'admin') {
        flash('error', 'Only administrators can manage staff accounts.');
        redirect('/admin/staff');
    }

    $v = \Validator::make($_POST, [
        'name' => 'required', 'email' => 'required|email', 'password' => 'required|min:8',
        'role' => 'required|in:staff,admin',
    ]);

    if ($v->fails()) {
        flash('error', $v->firstError());
        redirect('/admin/staff');
    }

    if (User::findByEmail($_POST['email'])) {
        flash('error', 'An account with this email already exists.');
        redirect('/admin/staff');
    }

    $firebase = new FirebaseService();
    $auth = $firebase->signUp(strtolower($_POST['email']), $_POST['password']);

    if (isset($auth['error'])) {
        flash('error', 'Could not create account: ' . ($auth['error']['message'] ?? 'unknown error'));
        redirect('/admin/staff');
    }

    User::create([
        'uid' => $auth['localId'] ?? null,
        'name' => $_POST['name'],
        'email' => strtolower($_POST['email']),
        'phone' => $_POST['phone'] ?? '',
        'role' => $_POST['role'],
        'status' => 'active',
    ]);

    flash('success', 'Staff account created.');
    redirect('/admin/staff');
}

$staff = User::staffAndAdmins(200);
$title = 'Staff Management';
ob_start();
?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="admin-panel">
            <div class="admin-panel-head"><h3>Staff & Admin Accounts (<?= count($staff) ?>)</h3></div>
            <div class="admin-panel-body p-0">
                <?php if (empty($staff)): ?>
                    <div class="empty-state"><i class="fa-solid fa-user-tie"></i><h4>No staff accounts yet</h4></div>
                <?php else: ?>
                    <table class="table-dtc w-100">
                        <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr>
                        <?php foreach ($staff as $s): ?>
                            <tr>
                                <td><?= e($s['name']) ?></td>
                                <td><?= e($s['email']) ?></td>
                                <td><span class="badge-pill badge-info"><?= ucfirst($s['role']) ?></span></td>
                                <td><span class="badge-pill <?= ($s['status'] ?? 'active') === 'active' ? 'badge-success' : 'badge-danger' ?>"><?= ucfirst($s['status'] ?? 'active') ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="admin-panel"><div class="admin-panel-body">
            <h6 class="mb-3">Add Staff Member</h6>
            <?php if (user_role() !== 'admin'): ?>
                <p class="text-muted-dtc small">Only administrators can add staff accounts.</p>
            <?php else: ?>
                <form action="/admin/staff" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3"><label class="form-label">Full Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Temporary Password</label><input type="password" name="password" class="form-control" minlength="8" required></div>
                    <div class="mb-4">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-copper w-100">Create Account</button>
                </form>
            <?php endif; ?>
        </div></div>
    </div>
</div>

<?php
$content = ob_get_clean();
require base_path('admin/layout.php');
