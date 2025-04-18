<?php

namespace Database\Seeders;

use App\Models\Animals;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnimalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $animals = [
            [
                'name' => 'Persian Cat',
                'slug' => 'persian-cat',
                'barcode' => 'ANIMAL001',
                'category_animals_id' => 1,
                'breeds_id' => 1,
                'age' => '1 year',
                'weight' => 3500,
                'gender' => 'female',
                'health_status' => 'Excellent',
                'vaccination_status' => true,
                'thumbnail' => 'animals/persian-cat.jpg',
                'description' => 'Beautiful Persian cat with blue eyes',
                'stock' => 2,
                'purchase_price' => 1500000,
                'selling_price' => 2500000,
                'is_active' => true,
            ],
            [
                'name' => 'Golden Retriever Puppy',
                'slug' => 'golden-retriever-puppy',
                'barcode' => 'ANIMAL002',
                'category_animals_id' => 2,
                'breeds_id' => 4,
                'age' => '3 months',
                'weight' => 5000,
                'gender' => 'male',
                'health_status' => 'Good',
                'vaccination_status' => true,
                'thumbnail' => 'animals/golden-retriever.jpg',
                'description' => 'Friendly golden retriever puppy',
                'stock' => 1,
                'purchase_price' => 3000000,
                'selling_price' => 4500000,
                'is_active' => true,
            ],
            [
                'name' => 'Lovebird Pair',
                'slug' => 'lovebird-pair',
                'barcode' => 'ANIMAL003',
                'category_animals_id' => 3,
                'breeds_id' => 7,
                'age' => '6 months',
                'weight' => 100,
                'gender' => 'unknown',
                'health_status' => 'Excellent',
                'vaccination_status' => false,
                'thumbnail' => 'animals/lovebird.jpg',
                'description' => 'Beautiful pair of lovebirds',
                'stock' => 3,
                'purchase_price' => 500000,
                'selling_price' => 800000,
                'is_active' => true,
            ],
        ];

        foreach ($animals as $animal) {
            Animals::create($animal);
        }
    }
}
