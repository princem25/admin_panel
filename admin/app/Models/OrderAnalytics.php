<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAnalytics extends Model
{
    protected $connection = 'analytics';
    protected $table = 'order_analytics';
    protected $fillable = [
        'total_orders',
        'total_revenue',
    ];
}
