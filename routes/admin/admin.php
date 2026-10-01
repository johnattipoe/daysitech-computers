<?php

use App\Controllers\ReviewController;
use App\Controllers\AuthController;

/** @var \App\Helpers\Router $router */

$router->get('/admin/login', [AuthController::class, 'showAdminLogin']);
$router->post('/admin/login', [AuthController::class, 'adminLogin']);

/**
 * The admin panel pages under /admin live as self-contained PHP files
 * (see the admin/ directory) rather than controller classes — each file
 * fetches its own data and calls admin_view(). This closure just resolves
 * the incoming /admin/... path to the right file after the admin
 * middleware has confirmed the user is staff/admin.
 */
$adminPage = function (string $file) {
    return function (...$params) use ($file) {
        $GLOBALS['adminRouteParams'] = $params;
        require resolve_php_file(base_path('admin'), $file);
    };
};

$router->group('/admin', ['admin'], function ($router) use ($adminPage) {

    $router->get('', $adminPage('index'));
    $router->get('/dashboard', $adminPage('dashboard/index'));

    // Products
    $router->get('/products', $adminPage('products/index'));
    $router->get('/products/create', $adminPage('products/create'));
    $router->post('/products/create', $adminPage('products/create'));
    $router->get('/products/edit/{id}', $adminPage('products/edit'));
    $router->post('/products/edit/{id}', $adminPage('products/edit'));
    $router->get('/products/categories', $adminPage('products/categories'));
    $router->post('/products/categories', $adminPage('products/categories'));

    // Orders
    $router->get('/orders', $adminPage('orders/index'));
    $router->get('/orders/{id}', $adminPage('orders/details'));
    $router->post('/orders/{id}', $adminPage('orders/details'));

    // Repairs
    $router->get('/repairs', $adminPage('repairs/index'));
    $router->get('/repairs/{id}', $adminPage('repairs/details'));
    $router->post('/repairs/{id}/update', $adminPage('repairs/update'));

    // Inventory
    $router->get('/inventory', $adminPage('inventory/index'));
    $router->get('/inventory/stock', $adminPage('inventory/stock'));
    $router->post('/inventory/stock', $adminPage('inventory/stock'));
    $router->get('/inventory/movements', $adminPage('inventory/movements'));

    // Customers
    $router->get('/customers', $adminPage('customers/index'));
    $router->get('/customers/{id}', $adminPage('customers/details'));
    $router->post('/customers/{id}', $adminPage('customers/details'));

    // Payments
    $router->get('/payments', $adminPage('payments/index'));

    // Reviews
    $router->get('/reviews', $adminPage('reviews/index'));
    $router->post('/reviews/{id}/approve', function ($id) {
        \App\Models\Review::approve($id);
        flash('success', 'Review approved.');
        redirect('/admin/reviews');
    });
    $router->post('/reviews/{id}/delete', [ReviewController::class, 'destroy']);

    // Staff
    $router->get('/staff', $adminPage('staff/index'));
    $router->post('/staff', $adminPage('staff/index'));

    // Reports
    $router->get('/reports/sales', $adminPage('reports/sales'));
    $router->get('/reports/inventory', $adminPage('reports/inventory'));
    $router->get('/reports/repairs', $adminPage('reports/repairs'));

    // Settings
    $router->get('/settings', $adminPage('settings/index'));
    $router->post('/settings', $adminPage('settings/index'));
});
