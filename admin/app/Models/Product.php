<?php

namespace App\Models;

use App\Collections\ProductCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'description',
        'price',
        'discount_price',
        'stock',
        'type',
        'image',
        'is_featured',
        'discount',
    ];

    /**
     * Override to return custom ProductCollection
     */
    public function newCollection(array $models = [])
    {
        return new ProductCollection($models);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where('name', 'like', '%'.$search.'%');
        });

        $query->when($filters['category'] ?? false, function ($query, $category) {
            $query->where('category_id', $category);
        });

        $query->when($filters['price'] ?? false, function ($query, $price) {
            $query->where('price', '<=', $price);
        });
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
