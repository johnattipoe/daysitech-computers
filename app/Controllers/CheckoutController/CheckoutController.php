<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Services\OrderService;
use App\Services\PaymentService;

class CheckoutController
{
    protected OrderService $orders;
    protected PaymentService $payments;

    public function __construct()
    {
        $this->orders = new OrderService();
        $this->payments = new PaymentService();
    }

    public function index(): void
    {
        if (empty(Cart::items())) {
            flash('error', 'Your cart is empty.');
            redirect('/products');
        }

        view('checkout.index', [
            'title'      => 'Checkout',
            'items'      => Cart::detailed(),
            'total'      => Cart::total(),
            'user'       => current_user(),
            'pageScript' => 'checkout.js',
        ]);
    }

    public function process(): void
    {
        require_csrf();

        $v = \Validator::make($_POST, [
            'name'    => 'required',
            'email'   => 'required|email',
            'phone'   => 'required|phone',
            'address' => 'required',
            'city'    => 'required',
        ]);

        if ($v->fails()) {
            flash('error', $v->firstError());
            redirect('/checkout');
        }

        $customer = [
            'name'    => $_POST['name'],
            'email'   => $_POST['email'],
            'phone'   => $_POST['phone'],
            'address' => $_POST['address'],
            'city'    => $_POST['city'],
        ];

        $paymentMethod = $_POST['payment_method'] ?? PAYMENT_METHOD_CASH;

        $result = $this->orders->placeOrder($customer, $paymentMethod, $_POST['notes'] ?? null);

        if (!$result['success']) {
            flash('error', $result['message']);
            redirect('/checkout');
        }

        $order = $result['order'];

        if ($paymentMethod === PAYMENT_METHOD_CARD || $paymentMethod === PAYMENT_METHOD_MOBILE_MONEY) {
            $init = $this->payments->initialize($order, $customer['email']);
            if (!empty($init['data']['authorization_url'])) {
                redirect($init['data']['authorization_url']);
            }
            flash('error', 'Could not start payment. Please try again or choose cash on delivery.');
            redirect('/checkout');
        }

        redirect('/checkout/success?order=' . $order['order_number']);
    }

    public function verify(): void
    {
        $reference = $_GET['order'] ?? $_GET['reference'] ?? '';
        $result = $this->payments->verify($reference);

        if ($result['success']) {
            redirect('/checkout/success?order=' . $reference);
        }

        flash('error', 'Payment could not be verified. If you were charged, please contact support.');
        redirect('/checkout');
    }

    public function success(): void
    {
        $orderNumber = $_GET['order'] ?? '';
        $order = \App\Models\Order::findByOrderNumber($orderNumber);

        if (!$order) {
            redirect('/');
        }

        view('checkout.success', ['title' => 'Order Confirmed', 'order' => $order], 'main');
    }
}
