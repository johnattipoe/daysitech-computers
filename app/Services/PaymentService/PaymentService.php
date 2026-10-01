<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Order;

/**
 * PaymentService — integrates Paystack (supports card + mobile money,
 * which covers the Ghanaian market well). Swap the base URL / methods
 * out if you prefer Flutterwave or Stripe.
 */
class PaymentService
{
    protected string $secretKey;
    protected string $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = env('PAYSTACK_SECRET_KEY', '');
    }

    public function initialize(array $order, string $email): array
    {
        $payload = [
            'email'        => $email,
            'amount'       => (int) round($order['total'] * 100), // pesewas/kobo
            'currency'     => env('PAYMENT_CURRENCY', 'GHS'),
            'reference'    => $order['order_number'],
            'callback_url' => url('checkout/verify?order=' . $order['order_number']),
            'metadata'     => ['order_id' => $order['id'], 'order_number' => $order['order_number']],
        ];

        $response = $this->request('POST', '/transaction/initialize', $payload);

        Payment::create([
            'order_id'  => $order['id'],
            'user_id'   => $order['user_id'] ?? null,
            'amount'    => $order['total'],
            'method'    => PAYMENT_METHOD_CARD,
            'provider_reference' => $order['order_number'],
            'status'    => 'pending',
        ]);

        return $response;
    }

    public function verify(string $reference): array
    {
        $response = $this->request('GET', "/transaction/verify/{$reference}");

        $success = ($response['data']['status'] ?? null) === 'success';

        $order = Order::findByOrderNumber($reference);
        if ($order) {
            Order::update($order['id'], [
                'payment_status' => $success ? 'paid' : 'failed',
                'status'         => $success ? ORDER_STATUS_PAID : $order['status'],
            ]);

            foreach (Payment::forOrder($order['id']) as $payment) {
                Payment::markStatus($payment['id'], $success ? 'success' : 'failed', $response['data'] ?? []);
            }
        }

        return ['success' => $success, 'order' => $order, 'raw' => $response];
    }

    protected function request(string $method, string $path, array $body = []): array
    {
        $ch = curl_init($this->baseUrl . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->secretKey,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 20,
        ]);
        if (!empty($body)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }
        $raw = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            log_message('error', "PaymentService error: {$err}");
            return ['status' => false, 'message' => $err];
        }
        return json_decode($raw, true) ?: [];
    }
}
