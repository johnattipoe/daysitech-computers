<div class="dtc-breadcrumb">
    <div class="container-inner"><a href="/">Home</a> / <a href="/account/dashboard">Dashboard</a> / <span class="current">Profile</span></div>
</div>

<section class="section-sm">
    <div class="container-inner">
        <div class="row g-4">
            <div class="col-lg-3"><?php require base_path('views/account/_sidebar.php'); ?></div>
            <div class="col-lg-9">
                <h1 class="mb-4" style="font-size:1.5rem;">My Profile</h1>

                <form action="/account/profile" method="POST" class="summary-card">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?= e($profile['name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?= e($profile['email'] ?? '') ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= e($profile['phone'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="<?= e($profile['city'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2"><?= e($profile['address'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-copper mt-4">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</section>
