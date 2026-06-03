<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 500), // سعر بين 10 و 500
            'quantity' => $this->faker->numberBetween(1, 100),
            'category_id' => $this->faker->numberBetween(1, 10), // أرقام عشوائية للأصناف
            'priceAfterDiscount' => $this->faker->randomFloat(2, 5, 400),
            'DiscountPercentage' => $this->faker->numberBetween(5, 50),
            'item_image' => 'public/default_item.jpg', // مسار وهمي ثابت مؤقتاً
            'details_image' => json_encode(['public/detail1.jpg', 'public/detail2.jpg']),
            'company' => $this->faker->company(),
        ];
    }
}
