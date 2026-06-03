<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Category;
use App\Models\Item;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. إنشاء مستخدم الفحص الثابت (مع تعديل second_name)
        User::factory()->create([
            'first_name' => 'Test',
            'second_name' => 'User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '123456789',
        ]);

        // 2. إنشاء مستخدمين عشوائيين آخرين (مثلاً 5 مستخدمين)
        User::factory(5)->create();

        // 3. إنشاء أصناف حقيقية وهمية لمتجرك
        $categories = ['Monitors', 'Keyboards', 'Mice', 'Headsets', 'Graphic Cards'];
        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }

        // 4. توليد 30 منتج وهمي من المنتجات الواقعية التي جهزناها في الفاكتوري
        Item::factory(30)->create();
    }
}
