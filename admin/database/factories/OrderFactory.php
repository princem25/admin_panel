<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'total_amount' => $this->faker->randomFloat(2, 10, 500),
            'status' => 'pending',
            'payment_method' => 'cod',
            'full_name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'shipping_address' => $this->faker->address(),
            'tracking_number' => null,
            'admin_note' => null,
        ];
    }
}
