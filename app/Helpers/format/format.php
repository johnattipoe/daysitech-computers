<?php
/**
 * Formatting helpers for currency, dates, statuses etc.
 */

if (!function_exists('money')) {
    function money(int|float|string|null $amount): string
    {
        return config('app.currency_symbol') . number_format((float) $amount, 2);
    }
}

if (!function_exists('format_date')) {
    function format_date(int|float|string|null $date, string $format = 'd M Y'): string
    {
        if (empty($date)) return '—';
        $ts = is_numeric($date) ? (int) $date : strtotime((string) $date);
        return $ts ? date($format, $ts) : '—';
    }
}

if (!function_exists('format_datetime')) {
    function format_datetime(int|float|string|null $date): string
    {
        return format_date($date, 'd M Y, h:i A');
    }
}

if (!function_exists('time_ago')) {
    function time_ago(int|float|string|null $date): string
    {
        $ts = is_numeric($date) ? (int) $date : strtotime((string) $date);
        if (!$ts) return '—';
        $diff = time() - $ts;
        if ($diff < 60) return 'just now';
        $units = [31536000 => 'year', 2592000 => 'month', 86400 => 'day', 3600 => 'hour', 60 => 'minute'];
        foreach ($units as $seconds => $label) {
            $count = floor($diff / $seconds);
            if ($count >= 1) return $count . ' ' . $label . ($count > 1 ? 's' : '') . ' ago';
        }
        return 'just now';
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = trim(iconv('UTF-8', 'ASCII//TRANSLIT', $text) ?: $text, '-');
        $text = strtolower(preg_replace('~[^-\w]+~', '', $text));
        return $text ?: 'n-a';
    }
}

if (!function_exists('status_badge_class')) {
    /**
     * Maps an order/repair status string to a Bootstrap-ish badge class
     * used consistently across storefront + admin.
     */
    function status_badge_class(string $status): string
    {
        return match ($status) {
            'paid', 'delivered', 'completed', 'ready_for_pickup', 'approved', 'in_stock' => 'badge-success',
            'pending', 'processing', 'diagnosing', 'in_repair', 'testing', 'awaiting_approval', 'low_stock' => 'badge-warning',
            'cancelled', 'refunded', 'out_of_stock', 'rejected' => 'badge-danger',
            'shipped', 'booked', 'received' => 'badge-info',
            default => 'badge-neutral',
        };
    }
}

if (!function_exists('status_label')) {
    function status_label(string $status): string
    {
        return ucwords(str_replace('_', ' ', $status));
    }
}

if (!function_exists('truncate')) {
    function truncate(string $text, int $length = 100): string
    {
        $text = strip_tags($text);
        return strlen($text) > $length ? substr($text, 0, $length) . '…' : $text;
    }
}

if (!function_exists('star_rating_html')) {
    function star_rating_html(float $rating, int $max = 5): string
    {
        $html = '';
        for ($i = 1; $i <= $max; $i++) {
            $html .= $i <= round($rating) ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
        }
        return $html;
    }
}
