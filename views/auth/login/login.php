<?php $adminLogin = $adminLogin ?? false; ?>
<div class="auth-shell">
    <div class="auth-visual">
        <span class="eyebrow" style="margin-bottom:1rem;"><i class="fa-solid fa-shield-halved"></i> <?= $adminLogin ? 'Staff Access' : 'Secure Sign In' ?></span>
        <h2><?= $adminLogin ? 'Welcome to the Daysitech control room.' : 'Welcome back to Daysitech Computers.' ?></h2>
        <p><?= $adminLogin ? 'Sign in with an authorized staff or administrator account.' : 'Track your orders, follow repair tickets in real time, and check out faster next time.' ?></p>
    </div>
    <div class="auth-form-side">
        <div class="auth-card">
            <h1><?= $adminLogin ? 'Admin Sign In' : 'Sign In' ?></h1>
            <p class="subtitle"><?= $adminLogin ? 'Enter your staff credentials to continue.' : 'Enter your details to access your account.' ?></p>

            <form action="<?= $adminLogin ? '/admin/login' : '/login' ?>" method="POST">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label small" for="remember">Remember me</label>
                    </div>
                    <a href="/forgot-password" class="small">Forgot password?</a>
                </div>
                <button type="submit" class="btn btn-copper w-100">Sign In</button>
            </form>

            <?php if ($adminLogin): ?>
                <p class="text-center text-muted-dtc small mt-4"><a href="/login">Customer sign in</a></p>
            <?php else: ?>
                <p class="text-center text-muted-dtc small mt-4">Don't have an account? <a href="/register">Create one</a></p>
            <?php endif; ?>
        </div>
    </div>
</div>
