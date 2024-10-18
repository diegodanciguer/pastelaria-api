<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        DB::table('products')->insert([
            [
                'name' => 'Beef Pastel',
                'price' => 5.50,
                'image' => 'https://example.com/images/beef_pastel.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cheese Pastel',
                'price' => 6.00,
                'image' => 'https://example.com/images/cheese_pastel.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chicken Pastel',
                'price' => 7.00,
                'image' => 'https://example.com/images/chicken_pastel.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
