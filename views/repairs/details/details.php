<?php
// Reuse the tracking view's rich timeline UI for a direct ticket link.
$ticket = $repair['ticket_number'] ?? null;
require resolve_php_file(base_path('views/repairs'), 'track');
