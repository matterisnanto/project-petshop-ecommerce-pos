<?php

namespace Database\Seeders;

use App\Models\Hotel;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $hotels = [
            [
                'name' => 'Cat Luxury Hotel',
                'category_animals_id' => 1,
                'description' => 'Luxury boarding for your beloved cats with individual suites',
                'price_per_day' => 150000,
                'capacity' => 10,
                'thumbnail' => 'hotels/cat-luxury.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Dog Standard Kennel',
                'category_animals_id' => 2,
                'description' => 'Standard kennel for dogs with daily exercise',
                'price_per_day' => 100000,
                'capacity' => 15,
                'thumbnail' => 'hotels/dog-standard.jpg',
                'is_active' => true,
            ],
        ];

        foreach ($hotels as $hotel) {
            Hotel::create($hotel);
        }

        // Add hotel packages
        $hotel1 = Hotel::where('name', 'Cat Luxury Hotel')->first();
        $hotel1->packages()->createMany([
            ['name' => 'Weekly Package', 'price' => 900000, 'description' => '7 days stay with discount'],
            ['name' => 'Monthly Package', 'price' => 3000000, 'description' => '30 days stay with big discount'],
        ]);

        $hotel2 = Hotel::where('name', 'Dog Standard Kennel')->first();
        $hotel2->packages()->createMany([
            ['name' => 'Weekend Package', 'price' => 180000, 'description' => '2 days stay'],
            ['name' => 'Weekly Package', 'price' => 600000, 'description' => '7 days stay with discount'],
        ]);
    }
}
