<?php

namespace Database\Seeders;

use App\Models\Breeds;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BreedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $breeds = [
            ['category_animals_id' => 1, 'name' => 'Persian', 'slug' => 'persian'],
            ['category_animals_id' => 1, 'name' => 'Siamese', 'slug' => 'siamese'],
            ['category_animals_id' => 1, 'name' => 'Maine Coon', 'slug' => 'maine-coon'],
            ['category_animals_id' => 2, 'name' => 'Golden Retriever', 'slug' => 'golden-retriever'],
            ['category_animals_id' => 2, 'name' => 'Bulldog', 'slug' => 'bulldog'],
            ['category_animals_id' => 2, 'name' => 'Poodle', 'slug' => 'poodle'],
            ['category_animals_id' => 3, 'name' => 'Lovebird', 'slug' => 'lovebird'],
            ['category_animals_id' => 3, 'name' => 'Cockatiel', 'slug' => 'cockatiel'],
        ];

        foreach ($breeds as $breed) {
            Breeds::create($breed);
        }
    }
}
