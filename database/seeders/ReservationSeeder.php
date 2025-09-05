<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Facility;
use Illuminate\Support\Str;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();         // first created user
        $facility = Facility::first(); // first facility

        if ($user && $facility) {
            Reservation::create([
                'user_id'           => $user->id,
                'facility_id'       => $facility->id,
                'date'              => '2025-09-10', // fixed date
                'start_time'        => '13:00',
                'end_time'          => '14:00',
                'fee'               => 150,
                'status'            => 'confirmed',
                'reservation_token' => Str::uuid(),
                'digital_signature' => null,
                'payment_id'        => null,
            ]);
        }
    }
}
