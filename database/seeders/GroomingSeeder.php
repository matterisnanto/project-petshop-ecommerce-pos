<?php

namespace Database\Seeders;

use App\Models\Grooming;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GroomingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $groomings = [
            [
                'name' => 'Cat Basic Grooming',
                'slug' => 'cat-basic-grooming',
                'category_animals_id' => 1,
                'category_grooming_id' => 1,
                'photo' => 'grooming/cat-basic.jpg',
                'description' => 'Basic grooming service for cats including bath, brushing, and nail trimming',
                'stock' => 0,
                'purchase_price' => null,
                'selling_price' => 150000,
                'is_active' => true,
            ],
            [
                'name' => 'Dog Premium Grooming',
                'slug' => 'dog-premium-grooming',
                'category_animals_id' => 2,
                'category_grooming_id' => 2,
                'photo' => 'grooming/dog-premium.jpg',
                'description' => 'Premium grooming service for dogs including spa treatment, teeth brushing, and aromatherapy',
                'stock' => 0,
                'purchase_price' => null,
                'selling_price' => 300000,
                'is_active' => true,
            ],
        ];

        foreach ($groomings as $grooming) {
            Grooming::create($grooming);
        }

        // Add grooming packages
        $grooming1 = Grooming::where('slug', 'cat-basic-grooming')->first();
        $grooming1->packages()->createMany([
            ['name' => 'Bath Only', 'price' => 80000, 'description' => 'Basic bath service'],
            ['name' => 'Bath + Nail Trim', 'price' => 120000, 'description' => 'Bath with nail trimming'],
        ]);

        $grooming2 = Grooming::where('slug', 'dog-premium-grooming')->first();
        $grooming2->packages()->createMany([
            ['name' => 'Full Spa Treatment', 'price' => 350000, 'description' => 'Includes massage and aromatherapy'],
            ['name' => 'Dental Care Package', 'price' => 250000, 'description' => 'Teeth brushing and dental check'],
        ]);
    }
}
