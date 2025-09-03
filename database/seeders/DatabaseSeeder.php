<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = Category::factory(5)->create();

        User::factory(5)
        ->has(Product::factory(100)->state(
            fn () => ['category_id' => $categories->random()->id]
        ))
        ->create();
    }
}