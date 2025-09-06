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
        FacilitiesSeeder::class,
        FacilityFeesSeeder::class,
        UserSeeder::class,
        ReservationSeeder::class,
    ]);
    }
}
