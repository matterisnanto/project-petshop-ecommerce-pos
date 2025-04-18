<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $brands = [
            ['name' => 'Royal Canin', 'slug' => 'royal-canin', 'logo' => 'brands/royal-canin.png'],
            ['name' => 'Whiskas', 'slug' => 'whiskas', 'logo' => 'brands/whiskas.png'],
            ['name' => 'Pedigree', 'slug' => 'pedigree', 'logo' => 'brands/pedigree.png'],
            ['name' => 'Friskies', 'slug' => 'friskies', 'logo' => 'brands/friskies.png'],
            ['name' => 'Pro Plan', 'slug' => 'pro-plan', 'logo' => 'brands/pro-plan.png'],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
