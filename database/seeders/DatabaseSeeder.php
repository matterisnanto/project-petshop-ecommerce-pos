<?php

namespace Database\Seeders;

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
        // User::factory(10)->create();
        $this->call([
            BrandSeeder::class,
            CategorySeeder::class,
            SupplierSeeder::class,
            PromoCodeSeeder::class,
            PaymentMethodSeeder::class,
            ProductSeeder::class,
            CategoryAnimalSeeder::class,
            BreedSeeder::class,
            AnimalSeeder::class,
            // CategoryGroomingSeeder::class,
            // GroomingSeeder::class,
            // HotelSeeder::class,
        ]);
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
