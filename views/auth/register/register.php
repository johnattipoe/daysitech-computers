<div class="auth-shell">
    <div class="auth-visual">
        <span class="eyebrow" style="margin-bottom:1rem;"><i class="fa-solid fa-user-plus"></i> Join Daysitech</span>
        <h2>Create your account in under a minute.</h2>
        <p>Get order tracking, repair updates, and a saved cart across all your devices.</p>
    </div>
    <div class="auth-form-side">
        <div class="auth-card">
            <div class="auth-kicker">
                <span class="auth-kicker-icon"><i class="fa-solid fa-user-plus" aria-hidden="true"></i></span>
                <span>CREATE YOUR ACCOUNT</span>
            </div>
            <h1>Create Account</h1>
            <p class="subtitle">It's free and only takes a moment.</p>

            <form action="/register" method="POST" class="auth-form auth-register-form">
                <?= csrf_field() ?>
                <div class="auth-register-identity-fields">
                    <div class="auth-register-field">
                        <label class="form-label" for="register-name">Full Name</label>
                        <input id="register-name" type="text" name="name" class="form-control" autocomplete="name" value="<?= e(old('name')) ?>" required>
                    </div>
                    <div class="auth-register-field">
                        <label class="form-label" for="register-email">Email Address</label>
                        <input id="register-email" type="email" name="email" class="form-control" autocomplete="email" value="<?= e(old('email')) ?>" required>
                    </div>
                </div>
                <div class="auth-register-field">
                    <label class="form-label" for="register-phone">Phone</label>
                    <input id="register-phone" type="tel" name="phone" class="form-control" autocomplete="tel" value="<?= e(old('phone')) ?>" required>
                </div>
                <div class="auth-password-fields">
                    <div class="auth-register-field">
                        <label class="form-label" for="register-password">Password</label>
                        <input id="register-password" type="password" name="password" class="form-control" autocomplete="new-password" minlength="8" required>
                    </div>
                    <div class="auth-register-field">
                        <label class="form-label" for="register-password-confirmation">Confirm password</label>
                        <input id="register-password-confirmation" type="password" name="password_confirmation" class="form-control" autocomplete="new-password" required>
                    </div>
                </div>
                <div class="form-check auth-register-terms">
                    <input class="form-check-input" type="checkbox" id="agree" required>
                    <label class="form-check-label small" for="agree">I agree to the <a href="/terms">Terms of Service</a> and <a href="/privacy">Privacy Policy</a></label>
                </div>
                <button type="submit" class="btn btn-copper w-100">Create Account</button>
            </form>

            <p class="auth-register-footer text-center text-muted-dtc small">Already have an account? <a href="/login">Sign in</a></p>
        </div>
    </div>
</div>
