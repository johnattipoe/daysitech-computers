<?php

require_once dirname(__DIR__, 2) . '/_bootstrap.php';

use App\Services\AuthService;

$method = api_method();
$input = api_input();

if ($method === 'GET') {
	$user = current_user();
	api_success(['user' => $user, 'authenticated' => $user !== null]);
}

if ($method !== 'POST') {
	api_require_method('GET', 'POST');
}

$action = strtolower((string) ($input['action'] ?? $_GET['action'] ?? 'login'));
$auth = new AuthService();

if ($action === 'logout') {
	$auth->logout();
	api_success(['message' => 'Signed out successfully.']);
}

if ($action === 'forgot-password') {
	$email = trim((string) ($input['email'] ?? ''));
	$validator = Validator::make(['email' => $email], ['email' => 'required|email']);
	if ($validator->fails()) api_error($validator->firstError(), 422);
	api_success(['message' => $auth->requestPasswordReset($email)['message']]);
}

if ($action === 'register') {
	$input = sanitize_input($input);
	$validator = Validator::make($input, [
		'name' => 'required|min:2',
		'email' => 'required|email',
		'phone' => 'required|phone',
		'password' => 'required|strong_password',
		'password_confirmation' => 'required',
	]);
	if ($validator->fails()) api_error($validator->firstError(), 422);
	if ($input['password'] !== $input['password_confirmation']) api_error('Passwords do not match.', 422);
	$result = $auth->register($input);
	if (!$result['success']) api_error($result['message'], 422);
	api_success(['user' => $result['user']], 201);
}

$validator = Validator::make($input, ['email' => 'required|email', 'password' => 'required']);
if ($validator->fails()) api_error($validator->firstError(), 422);
$result = $auth->attempt($input['email'], $input['password']);
if (!$result['success']) api_error($result['message'], 401);
api_success(['user' => $result['user'], 'message' => 'Signed in successfully.']);
