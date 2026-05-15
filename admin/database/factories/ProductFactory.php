<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        $price = fake()->randomFloat(2, 10, 1000);
        
        // Ensure discount price is less than price if generated
        $discountPrice = fake()->boolean(30) ? fake()->randomFloat(2, 1, $price - 1) : null;

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'price' => $price,
            'discount_price' => $discountPrice,
            'stock' => fake()->numberBetween(0, 100),
            'type' => fake()->randomElement(['physical', 'digital']),
            'image' => null, // Fallback handled in view
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'is_featured' => fake()->boolean(10), // 10% chance to be featured
            'discount' => $discountPrice ? ($price - $discountPrice) : 0,
        ];
    }
}
