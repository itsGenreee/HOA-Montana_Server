<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmenitiesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('amenities')->insert([
            [
                'name' => 'Chair',
                'price' => 8.00,
                'max_quantity' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // [
            //     'name' => 'Table',
            //     'price' => 50.00,
            //     'max_quantity' => 30,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            [
                'name' => 'Videoke',
                'price' => 700.00,
                'max_quantity' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Projector Set',
                'price' => 1000.00,
                'max_quantity' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
                        [
                'name' => 'Brides Room',
                'price' => 2000.00,
                'max_quantity' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
                        [
                'name' => 'Island Garden for Pictorial',
                'price' => 150.00,
                'max_quantity' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
