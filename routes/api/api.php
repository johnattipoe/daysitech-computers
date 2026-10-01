<?php

use App\Controllers\NotificationController;
use App\Controllers\CartController;
use App\Models\Product;
use App\Models\Repair;
use App\Models\Order;

/** @var \App\Helpers\Router $router */

// All routes here are prefixed with /api and return JSON.
$router->group('/api', [], function ($router) {

    // Products
    $router->get('/products/{slug}', function ($slug) {
        $product = Product::findBySlug($slug);
        json_response($product ? ['success' => true, 'product' => $product] : ['success' => false], $product ? 200 : 404);
    });

    // Cart
    $router->get('/cart/count', [CartController::class, 'count']);
    $router->post('/cart/add', [CartController::class, 'add']);

    // Repair ticket lookup (used by the track-repair AJAX form)
    $router->get('/repairs/track/{ticket}', function ($ticket) {
        $repair = Repair::findByTicket($ticket);
        json_response($repair ? ['success' => true, 'repair' => $repair] : ['success' => false, 'message' => 'Ticket not found.'], $repair ? 200 : 404);
    });

    // Order lookup
    $router->get('/orders/track/{orderNumber}', function ($orderNumber) {
        $order = Order::findByOrderNumber($orderNumber);
        json_response($order ? ['success' => true, 'order' => $order] : ['success' => false, 'message' => 'Order not found.'], $order ? 200 : 404);
    });

    // Notifications (authenticated)
    $router->get('/notifications', [NotificationController::class, 'index'], ['auth']);
    $router->post('/notifications/{id}/read', [NotificationController::class, 'markRead'], ['auth']);
    $router->post('/notifications/read-all', [NotificationController::class, 'markAllRead'], ['auth']);
});
