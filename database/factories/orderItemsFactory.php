<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Order;
use App\Models\orderItems;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<orderItems>
 */
class orderItemsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //'id' => $this->faker->uuid(),
           'order_id' => Order::factory(),
            'item_id' => Item::factory(),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'quantity' => $this->faker->randomFloat(2, 10, 500),
            'total_price' => $this->faker->randomFloat(2, 10, 500),
            'status' => $this->faker->randomElement(['pending', 'shipped', 'delivered']),
        ];
    }
}
