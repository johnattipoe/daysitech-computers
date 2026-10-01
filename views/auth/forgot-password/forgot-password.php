<div class="auth-shell">
    <div class="auth-visual">
        <span class="eyebrow" style="margin-bottom:1rem;"><i class="fa-solid fa-key"></i> Account Recovery</span>
        <h2>Forgot your password? No problem.</h2>
        <p>We'll email you a secure link to reset it.</p>
    </div>
    <div class="auth-form-side">
        <div class="auth-card">
            <h1>Reset Password</h1>
            <p class="subtitle">Enter the email associated with your account.</p>

            <form action="/forgot-password" method="POST">
                <?= csrf_field() ?>
                <div class="mb-4">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required autofocus>
                </div>
                <button type="submit" class="btn btn-copper w-100">Send Reset Link</button>
            </form>

            <p class="text-center text-muted-dtc small mt-4"><a href="/login"><i class="fa-solid fa-arrow-left me-1"></i>Back to Sign In</a></p>
        </div>
    </div>
</div>
