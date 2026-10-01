<div class="auth-shell">
    <div class="auth-visual">
        <span class="eyebrow" style="margin-bottom:1rem;"><i class="fa-solid fa-user-plus"></i> Join Daysitech</span>
        <h2>Create your account in under a minute.</h2>
        <p>Get order tracking, repair updates, and a saved cart across all your devices.</p>
    </div>
    <div class="auth-form-side">
        <div class="auth-card">
            <h1>Create Account</h1>
            <p class="subtitle">It's free and only takes a moment.</p>

            <form action="/register" method="POST">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?= e(old('name')) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?= e(old('email')) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?= e(old('phone')) ?>" required>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" minlength="8" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Confirm</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="agree" required>
                    <label class="form-check-label small" for="agree">I agree to the <a href="/terms">Terms of Service</a> and <a href="/privacy">Privacy Policy</a></label>
                </div>
                <button type="submit" class="btn btn-copper w-100">Create Account</button>
            </form>

            <p class="text-center text-muted-dtc small mt-4">Already have an account? <a href="/login">Sign in</a></p>
        </div>
    </div>
</div>
