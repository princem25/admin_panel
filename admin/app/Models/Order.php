<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total_amount',
        'payment_method',
        'status',
        'full_name',
        'phone',
        'shipping_address',
        'notes',
        'admin_note',
    ];

    /**
     * Get the status histories for the order.
     */
    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
