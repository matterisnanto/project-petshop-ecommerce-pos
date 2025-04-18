<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoryGrooming;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategoryGroomingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            ['name' => 'Basic Grooming', 'slug' => 'basic-grooming', 'description' => 'Basic grooming services', 'photo' => 'grooming/basic.jpg'],
            ['name' => 'Premium Grooming', 'slug' => 'premium-grooming', 'description' => 'Premium grooming services with extra care', 'photo' => 'grooming/premium.jpg'],
            ['name' => 'Medical Grooming', 'slug' => 'medical-grooming', 'description' => 'Grooming with medical treatment', 'photo' => 'grooming/medical.jpg'],
        ];

        foreach ($categories as $category) {
            CategoryGrooming::create($category);
        }
    }
}
