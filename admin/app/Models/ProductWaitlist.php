<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductWaitlist extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
    ];

    /**
     * The product this waitlist entry belongs to.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * The user waiting for this product.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
