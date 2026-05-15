<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'total_amount',
        'payment_method',
        'status',
        'full_name',
        'phone',
        'shipping_address',
        'tracking_number',
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
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Scope a query to only include genuine (completed/fulfilled) orders.
     */
    public function scopeGenuine($query)
    {
        return $query->whereNotIn('status', ['cancelled', 'pending']);
    }
}
