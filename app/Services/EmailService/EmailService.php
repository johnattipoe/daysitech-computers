<?php

namespace App\Services;

/**
 * EmailService — sends transactional emails via PHP's native mail() by
 * default (zero dependencies). Swap send() for an SMTP/PHPMailer or
 * an HTTP email API (Resend, SendGrid, Postmark) call in production —
 * everywhere else in the app calls the semantic methods below, so only
 * this file needs to change.
 */
class EmailService
{
    protected function send(string $to, string $subject, string $bodyHtml): bool
    {
        if (empty($to)) return false;

        $from = env('MAIL_FROM_ADDRESS', 'no-reply@daysitech.com');
        $fromName = env('MAIL_FROM_NAME', 'Daysitech Computers');

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$fromName} <{$from}>\r\n";

        try {
            return @mail($to, $subject, $bodyHtml, $headers);
        } catch (\Throwable $e) {
            log_message('error', 'EmailService: ' . $e->getMessage());
            return false;
        }
    }

    protected function wrap(string $title, string $bodyHtml): string
    {
        $business = config('app.business');
        return "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#f5f7fa;padding:24px;'>
            <div style='background:#0B1F3A;padding:20px;border-radius:8px 8px 0 0;text-align:center;'>
                <h2 style='color:#3DDC97;margin:0;'>{$business['name']}</h2>
            </div>
            <div style='background:#fff;padding:24px;border-radius:0 0 8px 8px;'>
                <h3 style='color:#0B1F3A;'>{$title}</h3>
                {$bodyHtml}
            </div>
            <p style='text-align:center;color:#8a94a6;font-size:12px;margin-top:16px;'>
                {$business['name']} &middot; {$business['address']} &middot; {$business['phone']}
            </p>
        </div>";
    }

    public function sendOrderConfirmation(string $to, array $order): bool
    {
        $rows = '';
        foreach ($order['items'] as $item) {
            $rows .= "<tr><td style='padding:6px 0;'>{$item['name']} x{$item['qty']}</td><td style='text-align:right;'>" . money($item['subtotal']) . "</td></tr>";
        }
        $body = "<p>Thanks for your order! Here's your summary:</p>
            <p><strong>Order #:</strong> {$order['order_number']}</p>
            <table style='width:100%;border-collapse:collapse;'>{$rows}
            <tr><td style='padding-top:10px;font-weight:bold;'>Total</td><td style='text-align:right;padding-top:10px;font-weight:bold;'>" . money($order['total']) . "</td></tr>
            </table>";

        return $this->send($to, "Order Confirmation — {$order['order_number']}", $this->wrap('Order Received', $body));
    }

    public function sendRepairConfirmation(string $to, array $repair): bool
    {
        $body = "<p>Your repair booking has been received.</p>
            <p><strong>Ticket #:</strong> {$repair['ticket_number']}<br>
            <strong>Device:</strong> {$repair['device_type']} {$repair['brand']} {$repair['model']}<br>
            <strong>Issue:</strong> " . e($repair['issue_description']) . "</p>
            <p>We'll notify you as our technicians update the status.</p>";

        return $this->send($to, "Repair Booking Confirmed — {$repair['ticket_number']}", $this->wrap('Repair Ticket Created', $body));
    }

    public function sendRepairStatusUpdate(string $to, array $repair): bool
    {
        $body = "<p>Your repair ticket <strong>{$repair['ticket_number']}</strong> has a new status:</p>
            <p style='font-size:18px;color:#3DDC97;font-weight:bold;'>" . status_label($repair['status']) . "</p>";

        return $this->send($to, "Repair Update — {$repair['ticket_number']}", $this->wrap('Repair Status Update', $body));
    }

    public function sendWelcome(string $to, string $name): bool
    {
        $body = "<p>Hi {$name}, welcome to Daysitech Computers! Browse our latest laptops, accessories, and book repairs anytime from your dashboard.</p>";
        return $this->send($to, "Welcome to Daysitech Computers", $this->wrap('Welcome Aboard 🎉', $body));
    }
}
