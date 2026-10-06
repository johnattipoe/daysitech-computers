<?php

namespace App\Controllers;

use App\Services\AuthService;

class AuthController
{
    protected AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService();
    }

    public function showLogin(): void
    {
        if (is_logged_in()) redirect(config('auth.guest_only_routes_redirect'));
        view('auth.login', ['title' => 'Sign In', 'pageScript' => 'auth.js'], 'auth');
    }

    public function showAdminLogin(): void
    {
        if (is_admin()) redirect('/admin/dashboard');
        view('auth.login', ['title' => 'Admin Sign In', 'adminLogin' => true, 'pageScript' => 'auth.js'], 'auth');
    }

    public function login(): void
    {
        require_csrf();

        if (!rate_limit('login_' . ($_SERVER['REMOTE_ADDR'] ?? ''), 8, 300)) {
            flash('error', 'Too many login attempts. Please wait a few minutes.');
            redirect('/login');
        }

        $v = \Validator::make($_POST, ['email' => 'required|email', 'password' => 'required']);
        if ($v->fails()) {
            flash('error', $v->firstError());
            redirect('/login');
        }

        $result = $this->auth->attempt($_POST['email'], $_POST['password']);

        if (!$result['success']) {
            flash('error', $result['message']);
            redirect('/login');
        }

        flash('success', 'Welcome back, ' . $result['user']['name'] . '!');
        $intended = $_SESSION['_intended'] ?? config('auth.redirects.' . $result['user']['role'], '/');
        unset($_SESSION['_intended']);
        redirect($intended);
    }

    public function adminLogin(): void
    {
        require_csrf();

        if (!rate_limit('admin_login_' . ($_SERVER['REMOTE_ADDR'] ?? ''), 8, 300)) {
            flash('error', 'Too many login attempts. Please wait a few minutes.');
            redirect('/admin/login');
        }

        $v = \Validator::make($_POST, ['email' => 'required|email', 'password' => 'required']);
        if ($v->fails()) {
            flash('error', $v->firstError());
            redirect('/admin/login');
        }

        $result = $this->auth->attempt($_POST['email'], $_POST['password'], ['admin', 'staff']);
        if (!$result['success']) {
            flash('error', $result['message']);
            redirect('/admin/login');
        }

        flash('success', 'Welcome to the admin panel, ' . $result['user']['name'] . '!');
        redirect('/admin/dashboard?admin_login=success');
    }

    public function showRegister(): void
    {
        if (is_logged_in()) redirect(config('auth.guest_only_routes_redirect'));
        view('auth.register', ['title' => 'Create Account', 'pageScript' => 'auth.js'], 'auth');
    }

    public function register(): void
    {
        require_csrf();
        $input = sanitize_input($_POST);

        $v = \Validator::make($input, [
            'name'     => 'required|min:2',
            'email'    => 'required|email',
            'phone'    => 'required|phone',
            'password' => 'required|strong_password',
            'password_confirmation' => 'required',
        ]);

        if ($v->fails()) {
            $_SESSION['_old'] = $input;
            flash('error', $v->firstError());
            redirect('/register');
        }

        if ($input['password'] !== $input['password_confirmation']) {
            flash('error', 'Passwords do not match.');
            redirect('/register');
        }

        $result = $this->auth->register($input);

        if (!$result['success']) {
            flash('error', $result['message']);
            redirect('/register');
        }

        flash('success', 'Account created! Welcome to Daysitech Computers.');
        redirect('/account/dashboard');
    }

    public function logout(): void
    {
        $this->auth->logout();
        flash('success', 'You have been signed out.');
        redirect('/');
    }

    public function showForgotPassword(): void
    {
        view('auth.forgot-password', ['title' => 'Forgot Password'], 'auth');
    }

    public function forgotPassword(): void
    {
        require_csrf();
        $result = $this->auth->requestPasswordReset($_POST['email'] ?? '');
        flash('success', $result['message']);
        redirect('/forgot-password');
    }
}
