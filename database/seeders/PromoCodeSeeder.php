<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\PromoCode;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PromoCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $promoCodes = [
            ['code' => 'WELCOME10', 'discount_amount' => 10, 'end_date' => Carbon::now()->addMonth()],
            ['code' => 'PETLOVER20', 'discount_amount' => 20, 'end_date' => Carbon::now()->addWeeks(2)],
            ['code' => 'FIRSTORDER', 'discount_amount' => 15, 'end_date' => Carbon::now()->addDays(7)],
        ];

        foreach ($promoCodes as $promoCode) {
            PromoCode::create($promoCode);
        }
    }
}
