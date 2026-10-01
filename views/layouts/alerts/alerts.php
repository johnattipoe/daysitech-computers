<?php
$success = flash('success');
$error = flash('error');
?>
<?php if ($success): ?>
    <div class="dtc-alert dtc-alert-success" data-flash="success" data-message="<?= e($success) ?>" style="display:none">
        <i class="fa-solid fa-circle-check"></i> <?= e($success) ?>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="dtc-alert dtc-alert-error" data-flash="error" data-message="<?= e($error) ?>" style="display:none">
        <i class="fa-solid fa-circle-exclamation"></i> <?= e($error) ?>
    </div>
<?php endif; ?>
