<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacilitiesSeeder extends Seeder
{
    public function run(): void
    {
        // Tennis Court (Hourly)
        DB::table('facilities')->insert([
            'name' => 'Tennis Court',
            'start_time' => '06:00:00',
            'end_time' => '22:00:00',
            'interval_minutes' => 60,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Basketball Court (Hourly)
        DB::table('facilities')->insert([
            'name' => 'Basketball Court',
            'start_time' => '06:00:00',
            'end_time' => '22:00:00',
            'interval_minutes' => 60,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Event Place (Blocks)
        DB::table('facilities')->insert([
            'name' => 'Event Place',
            'start_time' => null,
            'end_time' => null,
            'interval_minutes' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
