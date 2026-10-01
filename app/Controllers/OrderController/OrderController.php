<?php

namespace App\Controllers;

use App\Models\Order;
use App\Services\OrderService;

class OrderController
{
    protected OrderService $service;

    public function __construct()
    {
        $this->service = new OrderService();
    }

    public function index(): void
    {
        $user = current_user();
        view('account.orders', [
            'title'  => 'My Orders',
            'orders' => Order::forUser($user['id']),
        ]);
    }

    public function details(string $id): void
    {
        $order = Order::find($id);
        $user = current_user();

        if (!$order || ($order['user_id'] !== $user['id'] && !is_admin())) {
            http_response_code(404);
            view('pages.404', ['title' => 'Order Not Found']);
            return;
        }

        view('account.orders', ['title' => 'Order Details', 'order' => $order, 'single' => true, 'orders' => []]);
    }

    public function cancel(string $id): void
    {
        require_csrf();
        $result = $this->service->cancel($id, $_POST['reason'] ?? '');
        flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Order cancelled.' : $result['message']);
        redirect('/account/orders');
    }

    public function track(): void
    {
        $orderNumber = $_GET['order'] ?? '';
        $order = $orderNumber ? Order::findByOrderNumber($orderNumber) : null;
        view('account.orders', ['title' => 'Track Order', 'order' => $order, 'single' => (bool) $order, 'orders' => [], 'tracking' => true]);
    }
}
