<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $suppliers = [
            ['name' => 'PT Pet Supplies', 'email' => 'petsupplies@example.com', 'phone' => '081234567890', 'address' => 'Jl. Raya Pet Supplies No. 123, Jakarta'],
            ['name' => 'CV Animal Nutrition', 'email' => 'animalnutrition@example.com', 'phone' => '082345678901', 'address' => 'Jl. Animal Nutrition No. 456, Bandung'],
            ['name' => 'UD Pet Food Distributor', 'email' => 'petfood@example.com', 'phone' => '083456789012', 'address' => 'Jl. Pet Food No. 789, Surabaya'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
