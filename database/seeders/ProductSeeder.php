<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $products = [
            [
                'name' => 'Royal Canin Adult Cat Food',
                'slug' => 'royal-canin-adult-cat-food',
                'barcode' => '123456789012',
                'weight' => 1000,
                'thumbnail' => 'products/royal-canin-cat.jpg',
                'description' => 'Premium cat food for adult cats',
                'purchase_price' => 150000,
                'selling_price' => 200000,
                'is_active' => true,
                'is_popular' => true,
                'stock' => 50,
                'category_id' => 1,
                'brand_id' => 1,
            ],
            [
                'name' => 'Whiskas Wet Food Tuna',
                'slug' => 'whiskas-wet-food-tuna',
                'barcode' => '234567890123',
                'weight' => 200,
                'thumbnail' => 'products/whiskas-tuna.jpg',
                'description' => 'Delicious wet food with tuna flavor',
                'purchase_price' => 15000,
                'selling_price' => 25000,
                'is_active' => true,
                'is_popular' => true,
                'stock' => 100,
                'category_id' => 2,
                'brand_id' => 2,
            ],
            [
                'name' => 'Pedigree Adult Dog Food',
                'slug' => 'pedigree-adult-dog-food',
                'barcode' => '345678901234',
                'weight' => 3000,
                'thumbnail' => 'products/pedigree-dog.jpg',
                'description' => 'Complete nutrition for adult dogs',
                'purchase_price' => 180000,
                'selling_price' => 250000,
                'is_active' => true,
                'is_popular' => false,
                'stock' => 30,
                'category_id' => 1,
                'brand_id' => 3,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
