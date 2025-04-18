<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            ['name' => 'Dry Food', 'slug' => 'dry-food', 'description' => 'Dry food for pets', 'icon' => 'categories/dry-food.png'],
            ['name' => 'Wet Food', 'slug' => 'wet-food', 'description' => 'Wet food for pets', 'icon' => 'categories/wet-food.png'],
            ['name' => 'Treats', 'slug' => 'treats', 'description' => 'Delicious treats for pets', 'icon' => 'categories/treats.png'],
            ['name' => 'Toys', 'slug' => 'toys', 'description' => 'Fun toys for pets', 'icon' => 'categories/toys.png'],
            ['name' => 'Accessories', 'slug' => 'accessories', 'description' => 'Various pet accessories', 'icon' => 'categories/accessories.png'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
