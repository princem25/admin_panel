<?php

use Illuminate\Support\Number;

if (!function_exists('format_price')) {
    /**
     * Format a number to currency.
     *
     * @param float|int $amount
     * @param string $currency
     * @return string
     */
    function format_price($amount, $currency = null)
    {
        if (!$currency) {
            $currency = app()->getLocale() == 'ar' ? 'SAR' : 'INR';
        }
        return Number::currency($amount, $currency);
    }
}

if (!function_exists('order_status_badge')) {
    /**
     * Return the correct Tailwind class for an order status.
     *
     * @param string $status
     * @return string
     */
    function order_status_badge($status)
    {
        switch (strtolower($status)) {
            case 'pending':
                return 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-500/20 dark:text-yellow-400 dark:border-yellow-500/30';
            case 'processing':
                return 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-500/20 dark:text-blue-400 dark:border-blue-500/30';
            case 'shipped':
                return 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-500/20 dark:text-purple-400 dark:border-purple-500/30';
            case 'delivered':
                return 'bg-green-100 text-green-800 border-green-200 dark:bg-green-500/20 dark:text-green-400 dark:border-green-500/30';
            case 'cancelled':
                return 'bg-red-100 text-red-800 border-red-200 dark:bg-red-500/20 dark:text-red-400 dark:border-red-500/30';
            default:
                return 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-500/20 dark:text-gray-400 dark:border-gray-500/30';
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
