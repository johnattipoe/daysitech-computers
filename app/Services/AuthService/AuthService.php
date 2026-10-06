<?php

namespace App\Services;

use App\Models\User;
use App\Models\Cart;

/**
 * AuthService
 * Wraps Firebase Identity Toolkit (for password verification / hashing at
 * Google's end) with our own Firestore "users" profile document, then
 * manages the PHP session.
 */
class AuthService
{
    protected FirebaseService $firebase;

    public function __construct()
    {
        $this->firebase = new FirebaseService();
    }

    public function register(array $input): array
    {
        $email = strtolower(trim($input['email']));

        if (User::findByEmail($email)) {
            return ['success' => false, 'message' => 'An account with this email already exists.'];
        }

        $authResult = $this->firebase->signUp($email, $input['password']);

        if (isset($authResult['error'])) {
            return ['success' => false, 'message' => $this->friendlyAuthError($authResult['error']['message'] ?? '')];
        }

        $uid = $authResult['localId'] ?? '';
        if (!is_string($uid) || $uid === '') {
            return ['success' => false, 'message' => 'Account registration could not be completed. Please try again.'];
        }

        $profile = User::create([
            'uid'     => $uid,
            'name'    => trim($input['name']),
            'email'   => $email,
            'phone'   => $input['phone'] ?? '',
            'role'    => 'customer',
            'address' => $input['address'] ?? '',
            'city'    => $input['city'] ?? '',
            'status'  => 'active',
        ], $uid);

        if (empty($profile['id'])) {
            $profile = User::findByUid($uid) ?? [];
        }

        if (empty($profile['id'])) {
            return ['success' => false, 'message' => 'Your account was created, but its profile could not be saved. Please contact support before trying again.'];
        }

        if (!$this->login($profile)) {
            return ['success' => false, 'message' => 'Your account profile is incomplete. Please contact support.'];
        }

        return ['success' => true, 'user' => $profile];
    }

    public function login(array $profile): bool
    {
        $userId = $profile['id'] ?? null;
        if (!is_string($userId) || $userId === '') {
            return false;
        }

        $_SESSION[config('auth.session_key')] = User::toSessionArray($profile);
        session_regenerate_id(true);
        Cart::restoreForUser($userId);
        return true;
    }

    public function attempt(string $email, string $password, array $allowedRoles = []): array
    {
        $email = strtolower(trim($email));
        $authResult = $this->firebase->signIn($email, $password);

        if (isset($authResult['error'])) {
            return ['success' => false, 'message' => $this->friendlyAuthError($authResult['error']['message'] ?? '')];
        }

        $profile = User::findByEmail($email);
        if (!$profile) {
            return ['success' => false, 'message' => 'Account profile not found. Please contact support.'];
        }

        if (($profile['status'] ?? 'active') !== 'active') {
            return ['success' => false, 'message' => 'This account has been suspended. Please contact support.'];
        }

        if ($allowedRoles && !in_array($profile['role'] ?? 'customer', $allowedRoles, true)) {
            return ['success' => false, 'message' => 'This account does not have admin access.'];
        }

        if (!$this->login($profile)) {
            return ['success' => false, 'message' => 'Account profile is incomplete. Please contact support.'];
        }
        return ['success' => true, 'user' => $profile];
    }

    public function logout(): void
    {
        unset($_SESSION[config('auth.session_key')]);
        $_SESSION['cart'] = [];
        session_regenerate_id(true);
    }

    public function requestPasswordReset(string $email): array
    {
        $res = $this->firebase->sendPasswordResetEmail(strtolower(trim($email)));
        // Always respond success-shaped to avoid leaking which emails exist
        return ['success' => true, 'message' => 'If that email exists, a reset link has been sent.'];
    }

    protected function friendlyAuthError(string $code): string
    {
        return match (true) {
            str_contains($code, 'EMAIL_EXISTS') => 'An account with this email already exists.',
            str_contains($code, 'EMAIL_NOT_FOUND'), str_contains($code, 'INVALID_PASSWORD'), str_contains($code, 'INVALID_LOGIN_CREDENTIALS') => 'Incorrect email or password.',
            str_contains($code, 'WEAK_PASSWORD') => 'Password should be at least 6 characters.',
            str_contains($code, 'TOO_MANY_ATTEMPTS') => 'Too many attempts. Please try again later.',
            default => 'Something went wrong. Please try again.',
        };
    }
}
