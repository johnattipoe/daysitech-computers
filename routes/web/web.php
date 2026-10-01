<?php

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\ProductController;
use App\Controllers\CategoryController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\OrderController;
use App\Controllers\RepairController;
use App\Controllers\CustomerController;
use App\Controllers\ReviewController;

/** @var \App\Helpers\Router $router */

// -------- Public pages --------
$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [HomeController::class, 'about']);
$router->get('/contact', [HomeController::class, 'contact']);
$router->post('/contact', [HomeController::class, 'submitContact']);
$router->get('/terms', [HomeController::class, 'terms']);
$router->get('/privacy', [HomeController::class, 'privacy']);

// -------- Auth (guests) --------
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/forgot-password', [AuthController::class, 'showForgotPassword']);
$router->post('/forgot-password', [AuthController::class, 'forgotPassword']);

// -------- Products & categories --------
$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/search', [ProductController::class, 'search']);
$router->get('/products/compare', [ProductController::class, 'compare']);
$router->get('/products/{slug}', [ProductController::class, 'details']);
$router->get('/categories', [CategoryController::class, 'index']);
$router->get('/categories/{slug}', [CategoryController::class, 'show']);

// -------- Cart (session-based, guests allowed) --------
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);
$router->post('/cart/clear', [CartController::class, 'clear']);
$router->get('/cart/count', [CartController::class, 'count']);

// -------- Checkout --------
$router->get('/checkout', [CheckoutController::class, 'index']);
$router->post('/checkout', [CheckoutController::class, 'process']);
$router->get('/checkout/verify', [CheckoutController::class, 'verify']);
$router->get('/checkout/success', [CheckoutController::class, 'success']);

// -------- Repairs (booking/tracking public; ticket gates access to details) --------
$router->get('/repairs/book', [RepairController::class, 'showBooking']);
$router->post('/repairs/book', [RepairController::class, 'book']);
$router->get('/repairs/track', [RepairController::class, 'track']);
$router->get('/repairs/{id}', [RepairController::class, 'details']);

// -------- Reviews --------
$router->post('/reviews', [ReviewController::class, 'store'], ['auth']);

// -------- Authenticated customer account --------
$router->group('/account', ['auth'], function ($router) {
    $router->get('/dashboard', [CustomerController::class, 'dashboard']);
    $router->get('/profile', [CustomerController::class, 'profile']);
    $router->post('/profile', [CustomerController::class, 'updateProfile']);
    $router->get('/orders', [OrderController::class, 'index']);
    $router->get('/orders/{id}', [OrderController::class, 'details']);
    $router->post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    $router->get('/repairs', [CustomerController::class, 'repairs']);
    $router->get('/wishlist', [CustomerController::class, 'wishlist']);
});
