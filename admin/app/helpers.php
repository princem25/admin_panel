<?php

use Illuminate\Support\Number;

if (!function_exists('format_price')) {
    /**
     * Format a number to currency.
     *
     * @param float|int $amount
     * @return string
     */
    function format_price($amount)
    {
        return Number::currency($amount, 'INR');
    }
}

if (!function_exists('order_status_badge')) {
    /**
     * Return the correct bootstrap class for an order status.
     *
     * @param string $status
     * @return string
     */
    function order_status_badge($status)
    {
        switch (strtolower($status)) {
            case 'pending':
                return 'badge bg-warning';
            case 'paid':
                return 'badge bg-success';
            case 'shipped':
                return 'badge bg-info';
            case 'delivered':
                return 'badge bg-primary';
            default:
                return 'badge bg-secondary';
        }
    }
}

if (!function_exists('human_file_size')) {
    /**
     * Convert bytes into human readable format.
     *
     * @param int $bytes
     * @return string
     */
    function human_file_size($bytes)
    {
        if ($bytes <= 0) {
            return '0 B';
        }
        
        return Number::fileSize($bytes);
    }
}
