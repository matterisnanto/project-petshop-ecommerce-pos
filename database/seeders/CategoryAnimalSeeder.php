<?php

namespace Database\Seeders;

use App\Models\CategoryAnimals;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryAnimalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            ['name' => 'Cat', 'slug' => 'cat', 'description' => 'Feline pets', 'icon' => 'category-animals/cat.png'],
            ['name' => 'Dog', 'slug' => 'dog', 'description' => 'Canine pets', 'icon' => 'category-animals/dog.png'],
            ['name' => 'Bird', 'slug' => 'bird', 'description' => 'Avian pets', 'icon' => 'category-animals/bird.png'],
            ['name' => 'Fish', 'slug' => 'fish', 'description' => 'Aquatic pets', 'icon' => 'category-animals/fish.png'],
            ['name' => 'Rabbit', 'slug' => 'rabbit', 'description' => 'Lagomorph pets', 'icon' => 'category-animals/rabbit.png'],
        ];

        foreach ($categories as $category) {
            CategoryAnimals::create($category);
        }
    }
}
