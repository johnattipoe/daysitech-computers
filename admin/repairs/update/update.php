<?php

use App\Models\Repair;
use App\Services\RepairService;

$id = $GLOBALS['adminRouteParams'][0] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
    require_csrf();

    if (isset($_POST['estimated_cost']) && $_POST['estimated_cost'] !== '') {
        Repair::update($id, ['estimated_cost' => (float) $_POST['estimated_cost']]);
    }

    (new RepairService())->updateStatus($id, $_POST['status'], $_POST['technician_notes'] ?? null);

    flash('success', 'Repair ticket updated.');
}

redirect('/admin/repairs/' . $id);
