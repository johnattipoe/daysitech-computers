<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Product;

class CartController
{
    public function index(): void
    {
        view('cart.index', [
            'title'      => 'Your Cart',
            'items'      => Cart::detailed(),
            'total'      => Cart::total(),
            'pageScript' => 'cart.js',
        ]);
    }

    public function add(): void
    {
        require_csrf();
        $productId = $_POST['product_id'] ?? '';
        $qty = max(1, (int) ($_POST['qty'] ?? 1));

        $product = Product::find($productId);
        $isAjax = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') || !empty($_POST['ajax']);

        if (!$product || !Product::isInStock($product)) {
            $msg = 'This product is currently out of stock.';
            if ($isAjax) json_response(['success' => false, 'message' => $msg]);
            flash('error', $msg);
            redirect($_SERVER['HTTP_REFERER'] ?? '/products');
        }

        Cart::add($productId, $qty);

        if ($isAjax) {
            json_response(['success' => true, 'message' => 'Added to cart', 'count' => Cart::count()]);
        }

        flash('success', "{$product['name']} added to your cart.");
        redirect($_SERVER['HTTP_REFERER'] ?? '/cart');
    }

    public function update(): void
    {
        require_csrf();
        Cart::setQty($_POST['product_id'] ?? '', (int) ($_POST['qty'] ?? 1));
        flash('success', 'Cart updated.');
        redirect('/cart');
    }

    public function remove(): void
    {
        require_csrf();
        Cart::remove($_POST['product_id'] ?? '');
        flash('success', 'Item removed from cart.');
        redirect('/cart');
    }

    public function clear(): void
    {
        require_csrf();
        Cart::clear();
        redirect('/cart');
    }

    public function count(): void
    {
        json_response(['count' => Cart::count()]);
    }
}
