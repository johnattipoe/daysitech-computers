<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Repair;

class CustomerController
{
    public function dashboard(): void
    {
        $user = current_user();
        view('account.dashboard', [
            'title'   => 'My Dashboard',
            'orders'  => Order::forUser($user['id'], 5),
            'repairs' => Repair::forUser($user['id'], 5),
        ]);
    }

    public function profile(): void
    {
        $user = User::find(current_user()['id']);
        view('account.profile', ['title' => 'My Profile', 'profile' => $user]);
    }

    public function updateProfile(): void
    {
        require_csrf();
        $id = current_user()['id'];

        $v = \Validator::make($_POST, ['name' => 'required', 'phone' => 'required|phone']);
        if ($v->fails()) {
            flash('error', $v->firstError());
            redirect('/account/profile');
        }

        User::update($id, [
            'name'    => $_POST['name'],
            'phone'   => $_POST['phone'],
            'address' => $_POST['address'] ?? '',
            'city'    => $_POST['city'] ?? '',
        ]);

        $_SESSION[config('auth.session_key')]['name'] = $_POST['name'];
        flash('success', 'Profile updated successfully.');
        redirect('/account/profile');
    }

    public function repairs(): void
    {
        $user = current_user();
        view('account.repairs', ['title' => 'My Repairs', 'repairs' => Repair::forUser($user['id'])]);
    }

    public function wishlist(): void
    {
        view('account.wishlist', ['title' => 'My Wishlist']);
    }
}
