<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterPaymentMode;

class MasterPaymentModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentModes = [
            ['name' => 'Cash', 'code' => 'CASH', 'description' => 'Cash payments'],
            ['name' => 'Credit Card', 'code' => 'CARD', 'description' => 'Credit card payments'],
            ['name' => 'Debit Card', 'code' => 'CARD', 'description' => 'Debit card payments'],
            ['name' => 'Bank Transfer', 'code' => 'BANK', 'description' => 'Bank transfer payments'],
            ['name' => 'UPI', 'code' => 'UPI', 'description' => 'UPI payments'],
            ['name' => 'Digital Wallet', 'code' => 'WALLET', 'description' => 'Digital wallet payments'],
            ['name' => 'Cheque', 'code' => 'CHEQUE', 'description' => 'Cheque payments'],
            ['name' => 'Online Banking', 'code' => 'BANK', 'description' => 'Online banking payments'],
            ['name' => 'Other', 'code' => 'OTHER', 'description' => 'Other payment methods'],
        ];

        foreach ($paymentModes as $mode) {
            MasterPaymentMode::create($mode);
        }
    }
}
