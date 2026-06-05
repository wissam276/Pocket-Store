<?php

namespace Database\Seeders;

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
        // 1. إنشاء المستخدمين
        User::factory()->create([
            'first_name' => 'wissam',
            'second_name' => 'admin',
            'email'=>'wissam@ecommerce.com',
            'password'=>Hash::make('password'),
            'phone_number'=>'0123456789',
            'role'=>'admin',
        ]);
        User::factory(3)->create(['role' => 'customer']);

        // 2. إنشاء التصنيفات (حلقة واحدة فقط)
        foreach (Category::DEFAULT_CATEGORIES as $name) {
            $category = Category::create([
                'name' => trim($name),
                'slug' => Str::slug($name),
                'description' => 'وصف افتراضي لـ ' . $name,
            ]);

            // 3. إنشاء العناصر وربطها بالتصنيف الحالي
            Item::factory()
                ->count(3) // إنشاء 3 عناصر لكل تصنيف
                ->create([
                    'category_id' => $category->id,
                ]);
        }

    }
}
