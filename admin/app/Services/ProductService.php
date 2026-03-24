<?php
namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function all()
    {
        return Product::all();
    }
}