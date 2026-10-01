<div class="dtc-breadcrumb">
    <div class="container-inner"><a href="/">Home</a> / <a href="/account/dashboard">Dashboard</a> / <span class="current">My Repairs</span></div>
</div>

<section class="section-sm">
    <div class="container-inner">
        <div class="row g-4">
            <div class="col-lg-3"><?php require base_path('views/account/_sidebar.php'); ?></div>
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 style="font-size:1.5rem;" class="mb-0">My Repairs</h1>
                    <a href="/repairs/book" class="btn btn-copper btn-sm">Book New Repair</a>
                </div>

                <?php if (empty($repairs)): ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                        <h4>No repairs booked yet</h4>
                        <p>Book a repair and track it in real time right here.</p>
                        <a href="/repairs/book" class="btn btn-copper mt-3">Book a Repair</a>
                    </div>
                <?php else: ?>
                    <div class="admin-panel">
                        <div class="admin-panel-body p-0">
                            <table class="table-dtc w-100">
                                <tr><th>Ticket #</th><th>Device</th><th>Issue</th><th>Status</th><th></th></tr>
                                <?php foreach ($repairs as $r): ?>
                                    <tr>
                                        <td class="mono"><?= e($r['ticket_number']) ?></td>
                                        <td><?= e($r['device_type']) ?> <?= e($r['brand']) ?></td>
                                        <td><?= e(truncate($r['issue_description'], 40)) ?></td>
                                        <td><span class="badge-pill <?= status_badge_class($r['status']) ?>"><?= status_label($r['status']) ?></span></td>
                                        <td><a href="/repairs/track?ticket=<?= e($r['ticket_number']) ?>" class="btn btn-sm btn-ink">Track</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
