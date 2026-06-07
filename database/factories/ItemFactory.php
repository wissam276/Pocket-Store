<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $name = $this->faker->unique()->words(2, true);
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'slug' => Str::slug($name),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'quantity' => $this->faker->numberBetween(1, 100),
            'category_id' => Category::inRandomOrder()->value('id'),
            'priceAfterDiscount' => $this->faker->randomFloat(2, 5, 400),
            'DiscountPercentage' => $this->faker->numberBetween(5, 50),
            'item_image' => 'public/default_item.jpg',
            'details_image' => json_encode(['public/detail1.jpg', 'public/detail2.jpg']),
            'company' => $this->faker->company(),
        ];
    }
}
