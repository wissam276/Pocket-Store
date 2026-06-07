<?php

namespace Database\Factories;

use App\Models\order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<order>
 */
class orderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'total_price' => $this->faker->randomFloat(2, 10, 500),
            'status' => $this->faker->randomElement(['pending', 'shipped', 'delivered']),
            'shipping_address' => $this->faker->address,
            'payment_method' => 'COD',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function withItems(int $count = 3)
    {
        return $this->has(
            \App\Models\orderItems::factory()->count($count),
            'orderItems'
        );
    }
}
