<?php

require_once dirname(__DIR__, 2) . '/_bootstrap.php';

use App\Models\Repair;
use App\Services\RepairService;

$method = api_method();

if ($method === 'GET') {
	if (!empty($_GET['mine'])) {
		$user = api_require_auth();
		$repairs = Repair::forUser($user['id'], api_limit());
		api_success(['repairs' => $repairs, 'count' => count($repairs)]);
	}
	$ticket = trim((string) ($_GET['ticket'] ?? ''));
	if ($ticket === '') api_error('ticket is required.', 422);
	$repair = Repair::findByTicket($ticket);
	if (!$repair) api_error('Repair ticket not found.', 404);
	api_success(['repair' => $repair]);
}

api_require_method('POST');
$input = sanitize_input(api_input());
$validator = Validator::make($input, [
	'name' => 'required',
	'email' => 'required|email',
	'phone' => 'required|phone',
	'device_type' => 'required',
	'service_type' => 'required',
	'issue_description' => 'required|min:10',
]);
if ($validator->fails()) api_error($validator->firstError(), 422);
$repair = (new RepairService())->book($input);
api_success(['repair' => $repair], 201);
