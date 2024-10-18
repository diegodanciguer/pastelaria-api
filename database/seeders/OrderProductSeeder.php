<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderProductSeeder extends Seeder
{
    public function run()
    {
        DB::table('order_product')->insert([
            [
                'order_id' => 1,
                'product_id' => 1,
                'quantity' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 1,
                'product_id' => 2,
                'quantity' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'order_id' => 2,
                'product_id' => 3,
                'quantity' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
