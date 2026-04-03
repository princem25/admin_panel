<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function all()
    {
        return Product::with('category')->latest()->get();
    }

    public function exportCsv()
    {
        $products = Product::with('category')->get();

        return function () use ($products) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['Name', 'Price', 'Discount Price', 'Description', 'Stock']);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->name,
                    $product->price,
                    $product->discount_price ? $product->discount_price : $product->price,
                    $product->description,
                    $product->stock,
                ]);
            }

            fclose($file);
        };
    }

    public function getCsvString()
    {
        $products = Product::with('category')->get();

        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, ['Name', 'Price', 'Discount Price', 'Description', 'Stock']);

        foreach ($products as $product) {
            fputcsv($handle, [
                $product->name,
                $product->price,
                $product->discount_price ?? $product->price,
                $product->description,
                $product->stock,
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}
