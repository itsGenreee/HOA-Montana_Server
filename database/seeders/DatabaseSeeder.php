<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\FacilityFee;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
    $this->call([
        UserSeeder::class,
        FacilitiesSeeder::class,
        FacilityFeesSeeder::class,
        StaffSeeder::class,
        AmenitiesSeeder::class,
        ReservationSeeder::class,
    ]);
    }
}
