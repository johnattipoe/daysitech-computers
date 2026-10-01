<div class="page-header">
    <div class="container-inner">
        <h1>Track Your Repair</h1>
        <p>Enter your ticket number to see live status.</p>
    </div>
</div>

<section class="section-sm">
    <div class="container-inner" style="max-width:700px;">
        <form action="/repairs/track" method="GET" class="d-flex gap-2 mb-5">
            <input type="text" name="ticket" class="form-control" placeholder="e.g. DTC-RPR-240915-A1B2C" value="<?= e($ticket ?? '') ?>" required>
            <button type="submit" class="btn btn-copper"><i class="fa-solid fa-magnifying-glass me-1"></i>Track</button>
        </form>

        <?php if (!empty($ticket) && empty($repair)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-ticket"></i>
                <h4>Ticket not found</h4>
                <p>Double-check your ticket number and try again.</p>
            </div>
        <?php elseif (!empty($repair)): ?>
            <?php
            $steps = [REPAIR_STATUS_BOOKED, REPAIR_STATUS_RECEIVED, REPAIR_STATUS_DIAGNOSING, REPAIR_STATUS_IN_REPAIR, REPAIR_STATUS_TESTING, REPAIR_STATUS_READY, REPAIR_STATUS_COMPLETED];
            $currentIndex = array_search($repair['status'], $steps, true);
            ?>
            <div class="summary-card mb-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1">Ticket <?= e($repair['ticket_number']) ?></h5>
                        <p class="text-muted-dtc mb-0"><?= e($repair['device_type']) ?> <?= e($repair['brand']) ?> <?= e($repair['model']) ?></p>
                    </div>
                    <span class="badge-pill <?= status_badge_class($repair['status']) ?>"><?= status_label($repair['status']) ?></span>
                </div>
            </div>

            <div class="summary-card mb-4">
                <h6 class="mb-4">Repair Timeline</h6>
                <div class="timeline">
                    <?php foreach ($steps as $i => $step): ?>
                        <div class="timeline-item <?= $i < $currentIndex ? 'is-done' : ($i === $currentIndex ? 'is-current' : '') ?>">
                            <h6><?= status_label($step) ?></h6>
                            <?php if ($i === $currentIndex): ?><span>Current status</span><?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="summary-card">
                <h6 class="mb-3">Repair Details</h6>
                <div class="summary-line"><span>Issue</span><span><?= e($repair['issue_description']) ?></span></div>
                <div class="summary-line"><span>Service</span><span><?= e($repair['service_type']) ?></span></div>
                <div class="summary-line"><span>Drop-off Date</span><span><?= format_date($repair['drop_off_date']) ?></span></div>
                <?php if (!empty($repair['estimated_cost'])): ?>
                    <div class="summary-line"><span>Estimated Cost</span><span class="mono"><?= money($repair['estimated_cost']) ?></span></div>
                <?php endif; ?>
                <?php if (!empty($repair['technician_notes'])): ?>
                    <div class="summary-line"><span>Technician Notes</span><span><?= e($repair['technician_notes']) ?></span></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
