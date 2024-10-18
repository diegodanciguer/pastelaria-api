<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    public function run()
    {
        DB::table('clients')->insert([
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone' => '1234567890',
                'date_of_birth' => '1990-01-15',
                'address' => '123 Main St',
                'address_line2' => 'Apt 101',
                'neighborhood' => 'Downtown',
                'postal_code' => '12345',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'phone' => '0987654321',
                'date_of_birth' => '1985-05-10',
                'address' => '456 Oak St',
                'address_line2' => 'House',
                'neighborhood' => 'Uptown',
                'postal_code' => '67890',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
