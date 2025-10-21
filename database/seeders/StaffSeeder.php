<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('staffs')->insert([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'staff',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('staffs')->insert([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
