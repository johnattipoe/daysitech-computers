<div class="page-header">
    <div class="container-inner">
        <h1>Book a Repair</h1>
        <p>Tell us what's wrong — we'll diagnose and quote before touching a screw.</p>
    </div>
</div>

<section class="section-sm">
    <div class="container-inner" style="max-width:720px;">
        <form action="/repairs/book" method="POST" class="summary-card">
            <?= csrf_field() ?>

            <h6 class="mb-3">Your Contact Details</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?= e($user['name'] ?? old('name')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= e($user['email'] ?? old('email')) ?>" required>
                </div>
            </div>

            <h6 class="mb-3">Device Details</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Device Type</label>
                    <select name="device_type" class="form-select" required>
                        <option value="">Select device</option>
                        <?php foreach (($deviceTypes ?? []) as $type): ?>
                            <option value="<?= e($type) ?>"><?= e($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Service Needed</label>
                    <select name="service_type" class="form-select" required>
                        <option value="">Select service</option>
                        <?php foreach (($serviceTypes ?? []) as $type): ?>
                            <option value="<?= e($type) ?>"><?= e($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Brand</label>
                    <input type="text" name="brand" class="form-control" placeholder="e.g. HP, Dell, Lenovo">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Model</label>
                    <input type="text" name="model" class="form-control" placeholder="e.g. Pavilion 15">
                </div>
                <div class="col-12">
                    <label class="form-label">Describe the Issue</label>
                    <textarea name="issue_description" class="form-control" rows="4" placeholder="What's happening? When did it start?" required minlength="10"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-select">
                        <option value="normal">Normal</option>
                        <option value="urgent">Urgent (extra fee may apply)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Preferred Drop-off Date</label>
                    <input type="date" name="drop_off_date" class="form-control" min="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-copper btn-lg w-100">Submit Repair Booking <i class="fa-solid fa-arrow-right ms-1"></i></button>
            <p class="text-muted-dtc small text-center mt-3 mb-0">You'll receive a ticket number to track your repair's progress online.</p>
        </form>
    </div>
</section>
