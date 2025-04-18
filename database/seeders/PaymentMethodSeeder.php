<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $paymentMethods = [
            [
                'name' => 'Cash',
                'image' => 'payment-methods/cash.png',
                'olshop_transaction' => false,
                'pos_transaction' => true,
                'is_cash' => true
            ],
            [
                'name' => 'Bank Transfer - BCA',
                'account_number' => '1234567890',
                'image' => 'payment-methods/bca.png',
                'olshop_transaction' => true,
                'pos_transaction' => true,
                'is_cash' => false
            ],
            [
                'name' => 'Bank Transfer - Mandiri',
                'account_number' => '9876543210',
                'image' => 'payment-methods/mandiri.png',
                'olshop_transaction' => true,
                'pos_transaction' => true,
                'is_cash' => false
            ],
            [
                'name' => 'OVO',
                'account_number' => '081234567890',
                'image' => 'payment-methods/ovo.png',
                'olshop_transaction' => true,
                'pos_transaction' => true,
                'is_cash' => false
            ],
            [
                'name' => 'DANA',
                'account_number' => '082345678901',
                'image' => 'payment-methods/dana.png',
                'olshop_transaction' => true,
                'pos_transaction' => true,
                'is_cash' => false
            ],
        ];

        foreach ($paymentMethods as $paymentMethod) {
            PaymentMethod::create($paymentMethod);
        }
    }
}
