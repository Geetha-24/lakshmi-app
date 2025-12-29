<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PaymentModeMaster extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_mode_master')->insert([
            [
                "mode" => 'Cash',
                "status" => 0
            ],
            [
                "mode" => 'UPI',
                "status" => 0
            ],
            [
                "mode" => 'Credit',
                "status" => 0
            ],
            [
                "mode" => 'Cheque',
                "status" => 0
            ]
            ]);
    }
}
