<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        if ($users->isEmpty()) {
            $users = User::all(); // fallback if no role 'user' exists
        }
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->error('Please seed users and products first!');
            return;
        }

        for ($i = 0; $i < 100; $i++) {
            $user = $users->random();
            
            $order = Order::factory()->create([
                'user_id' => $user->id,
                'full_name' => $user->name,
            ]);
            
            // Create 1 to 4 random items for this order
            $randomProducts = $products->random(rand(1, 4));
            
            foreach ($randomProducts as $product) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3),
                    'price' => $product->price,
                ]);
            }
            
            // Update total amount based on items
            $total = $order->items->sum(function($item) {
                return $item->price * $item->quantity;
            });
            
            $order->update(['total_amount' => $total]);
        }
    }
}
