<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacilityFeesSeeder extends Seeder
{
    public function run(): void
    {
        // Tennis Court: flat rate only
        DB::table('facility_fees')->insert([
            'facility_id' => 1, // Tennis Court
            'type' => 'base',
            'fee' => 100,
            'discounted_fee' => 100,
            'start_time' => null,
            'end_time' => null,
            'name' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Basketball Court: day and night shift
        DB::table('facility_fees')->insert([
            'facility_id' => 2, //Basketball Court for Day Shift
            'type' => 'shift',
            'name' => 'day',
            'fee' => 100,
            'discounted_fee' => 100,
            'start_time' => '06:00:00',
            'end_time' => '18:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('facility_fees')->insert([
            'facility_id' => 2, //Basketball Court for Night Shift
            'type' => 'shift',
            'name' => 'night',
            'fee' => 250,
            'discounted_fee' => 250,
            'start_time' => '18:00:00',
            'end_time' => '22:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Event Place: blocks but base was used as a default value
        DB::table('facility_fees')->insert([
            'facility_id' => 3,
            'name' => 'Morning Event',
            'fee' => 12000,
            'discounted_fee' => 7000,
            'start_time' => '08:00:00',
            'end_time' => '13:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('facility_fees')->insert([
            'facility_id' => 3,
            'name' => 'Afternoon Event',
            'fee' => 12000,
            'discounted_fee' => 7000,
            'start_time' => '16:00:00',
            'end_time' => '21:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
