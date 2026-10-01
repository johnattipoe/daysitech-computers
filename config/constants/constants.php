<?php
/**
 * Application-wide constants.
 */

// Order status flow
define('ORDER_STATUS_PENDING', 'pending');
define('ORDER_STATUS_PAID', 'paid');
define('ORDER_STATUS_PROCESSING', 'processing');
define('ORDER_STATUS_SHIPPED', 'shipped');
define('ORDER_STATUS_DELIVERED', 'delivered');
define('ORDER_STATUS_CANCELLED', 'cancelled');
define('ORDER_STATUS_REFUNDED', 'refunded');

define('ORDER_STATUSES', [
    ORDER_STATUS_PENDING, ORDER_STATUS_PAID, ORDER_STATUS_PROCESSING,
    ORDER_STATUS_SHIPPED, ORDER_STATUS_DELIVERED, ORDER_STATUS_CANCELLED, ORDER_STATUS_REFUNDED,
]);

// Repair job status flow
define('REPAIR_STATUS_BOOKED', 'booked');
define('REPAIR_STATUS_RECEIVED', 'received');
define('REPAIR_STATUS_DIAGNOSING', 'diagnosing');
define('REPAIR_STATUS_AWAITING_APPROVAL', 'awaiting_approval');
define('REPAIR_STATUS_IN_REPAIR', 'in_repair');
define('REPAIR_STATUS_TESTING', 'testing');
define('REPAIR_STATUS_READY', 'ready_for_pickup');
define('REPAIR_STATUS_COMPLETED', 'completed');
define('REPAIR_STATUS_CANCELLED', 'cancelled');

define('REPAIR_STATUSES', [
    REPAIR_STATUS_BOOKED, REPAIR_STATUS_RECEIVED, REPAIR_STATUS_DIAGNOSING,
    REPAIR_STATUS_AWAITING_APPROVAL, REPAIR_STATUS_IN_REPAIR, REPAIR_STATUS_TESTING,
    REPAIR_STATUS_READY, REPAIR_STATUS_COMPLETED, REPAIR_STATUS_CANCELLED,
]);

// Repair device types & common services (used to build booking form)
define('REPAIR_DEVICE_TYPES', ['Laptop', 'Desktop', 'Printer', 'Monitor', 'Networking Equipment', 'Other']);
define('REPAIR_SERVICE_TYPES', [
    'Screen Replacement', 'Battery Replacement', 'Keyboard Repair', 'Liquid Damage',
    'Virus / Malware Removal', 'Data Recovery', 'OS Installation', 'Hardware Upgrade (RAM/SSD)',
    'Motherboard Repair', 'Power Issue', 'Networking Setup', 'General Diagnosis',
]);

// Payment methods
define('PAYMENT_METHOD_CARD', 'card');
define('PAYMENT_METHOD_MOBILE_MONEY', 'mobile_money');
define('PAYMENT_METHOD_CASH', 'cash_on_delivery');
define('PAYMENT_METHODS', [PAYMENT_METHOD_CARD, PAYMENT_METHOD_MOBILE_MONEY, PAYMENT_METHOD_CASH]);

// Inventory movement types
define('STOCK_IN', 'stock_in');
define('STOCK_OUT', 'stock_out');
define('STOCK_ADJUSTMENT', 'adjustment');
define('STOCK_RETURN', 'return');

// Notification types
define('NOTIFY_ORDER', 'order');
define('NOTIFY_REPAIR', 'repair');
define('NOTIFY_SYSTEM', 'system');
define('NOTIFY_PROMO', 'promo');

// Misc
define('LOW_STOCK_THRESHOLD', 5);
define('UPLOAD_MAX_SIZE_MB', 5);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
