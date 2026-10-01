<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Cart;
use App\Models\Product;

class OrderService
{
    protected InventoryService $inventory;
    protected NotificationService $notifications;
    protected EmailService $email;

    public function __construct()
    {
        $this->inventory = new InventoryService();
        $this->notifications = new NotificationService();
        $this->email = new EmailService();
    }

    /**
     * Places an order from the current cart. Returns ['success' => bool, 'order' => [...], 'message' => ...]
     */
    public function placeOrder(array $customer, string $paymentMethod, ?string $notes = null): array
    {
        $cartItems = Cart::detailed();

        if (empty($cartItems)) {
            return ['success' => false, 'message' => 'Your cart is empty.'];
        }

        // Re-validate stock right before checkout
        foreach ($cartItems as $row) {
            if ((int) $row['product']['stock'] < $row['qty']) {
                return ['success' => false, 'message' => "Sorry, \"{$row['product']['name']}\" only has {$row['product']['stock']} left in stock."];
            }
        }

        $subtotal = Cart::total();
        $shippingFee = $subtotal >= 500 ? 0 : 25; // simple free-shipping threshold
        $total = $subtotal + $shippingFee;

        $items = array_map(fn($row) => [
            'product_id' => $row['product']['id'],
            'name'       => $row['product']['name'],
            'price'      => (float) $row['product']['price'],
            'qty'        => $row['qty'],
            'subtotal'   => $row['subtotal'],
        ], $cartItems);

        $user = current_user();

        $order = Order::create([
            'order_number'   => generate_order_number(),
            'user_id'        => $user['id'] ?? null,
            'customer'       => $customer,
            'items'          => $items,
            'subtotal'       => $subtotal,
            'shipping_fee'   => $shippingFee,
            'total'          => $total,
            'status'         => ORDER_STATUS_PENDING,
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentMethod === PAYMENT_METHOD_CASH ? 'pending' : 'pending',
            'notes'          => $notes,
        ]);

        foreach ($cartItems as $row) {
            $this->inventory->recordSale($row['product']['id'], $row['product']['name'], $row['qty'], $order['order_number']);
        }

        Cart::clear();

        if ($user) {
            $this->notifications->orderPlaced($user['id'], $order);
        }
        $this->email->sendOrderConfirmation($customer['email'] ?? '', $order);

        return ['success' => true, 'order' => $order];
    }

    public function cancel(string $orderId, string $reason = ''): array
    {
        $order = Order::find($orderId);
        if (!$order) return ['success' => false, 'message' => 'Order not found.'];

        if (in_array($order['status'], [ORDER_STATUS_DELIVERED, ORDER_STATUS_CANCELLED], true)) {
            return ['success' => false, 'message' => 'This order can no longer be cancelled.'];
        }

        foreach ($order['items'] as $item) {
            Product::incrementStock($item['product_id'], $item['qty']);
        }

        $updated = Order::update($orderId, ['status' => ORDER_STATUS_CANCELLED, 'cancel_reason' => $reason]);
        return ['success' => true, 'order' => $updated];
    }

    public function updateStatus(string $orderId, string $status): array
    {
        $order = Order::updateStatus($orderId, $status);
        if (!empty($order['user_id'])) {
            $this->notifications->orderStatusChanged($order['user_id'], $order);
        }
        return $order;
    }
}
