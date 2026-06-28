<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\orderItems;
use App\Models\User;
use Database\Factories\ItemFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::updateOrCreate(
            ['email' => 'admin@pocketshop.com'],
            [
                'first_name' => 'Admin',
                'second_name' => 'User',
                'password' => Hash::make('admin123'),
                'phone_number' => '0123456789',
                'role' => 'admin',
                'address' => 'Damascus',
            ],
        );
        User::factory(3)->create(['role' => 'customer']);

        $this->call(CouponSeeder::class);

        foreach (Category::DEFAULT_CATEGORIES as $name) {
            $category = Category::create([
                'name' => trim($name),
                'slug' => Str::slug($name),
                'description' => 'virtual description ' . $name,
            ]);


            Item::factory()
                ->count(3)
                ->create([
                    'category_id' => $category->id,
                ]);
        }



        \App\Models\Order::factory(10)
            ->withItems(3)
            ->create();

    }
}
