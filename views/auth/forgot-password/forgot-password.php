<div class="auth-shell">
    <div class="auth-visual">
        <span class="eyebrow" style="margin-bottom:1rem;"><i class="fa-solid fa-key"></i> Account Recovery</span>
        <h2>Forgot your password? No problem.</h2>
        <p>We'll email you a secure link to reset it.</p>
    </div>
    <div class="auth-form-side">
        <div class="auth-card">
            <div class="auth-kicker">
                <span class="auth-kicker-icon"><i class="fa-solid fa-key" aria-hidden="true"></i></span>
                <span>ACCOUNT RECOVERY</span>
            </div>
            <h1>Reset Password</h1>
            <p class="subtitle">Enter the email associated with your account.</p>

            <form action="/forgot-password" method="POST" class="auth-form">
                <?= csrf_field() ?>
                <div class="mb-4">
                    <label class="form-label" for="recovery-email">Email Address</label>
                    <input id="recovery-email" type="email" name="email" class="form-control" autocomplete="email" required autofocus>
                </div>
                <button type="submit" class="btn btn-copper w-100">Send Reset Link</button>
            </form>

            <p class="auth-card-footer text-center text-muted-dtc small"><a href="/login"><i class="fa-solid fa-arrow-left me-1"></i>Back to Sign In</a></p>
        </div>
    </div>
</div>
