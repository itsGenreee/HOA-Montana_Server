<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Facility;
use Illuminate\Support\Str;
use App\Helpers\DigitalSignature;
use Illuminate\Support\Facades\DB;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $tennisCourt = Facility::where('name', 'Tennis Court')->first();
        $eventPlace  = Facility::where('name', 'Event Place')->first();

        if ($user && $tennisCourt) {
            // === Reservation #1 (Tennis Court) ===
            $reservationToken = Str::uuid()->toString();

            $digitalSignature = DigitalSignature::sign($reservationToken);

            $facilityFee = DB::table('facility_fees')
                ->where('facility_id', $tennisCourt->id)
                ->value('fee');

            Reservation::create([
                'user_id' => $user->id,
                'facility_id' => $tennisCourt->id,
                'date' => '2025-11-20',
                'start_time' => '13:00',
                'end_time' => '14:00',
                'facility_fee' => $facilityFee,
                'total_fee' => $facilityFee,
                'status' => 'confirmed',
                'event_type' => null,
                'guest_count' => null,
                'reservation_token' => $reservationToken,
                'digital_signature' => $digitalSignature,
                'payment_id' => null,
            ]);
        }

        if ($user && $eventPlace) {
            // === Reservation #2 (Event Place) ===
            $reservationToken = Str::uuid()->toString();

            $digitalSignature = DigitalSignature::sign($reservationToken);

            $facilityFee = DB::table('facility_fees')
                ->where('facility_id', $eventPlace->id)
                ->value('fee');

            // Create reservation first
            $reservation = Reservation::create([
                'user_id' => $user->id,
                'facility_id' => $eventPlace->id,
                'date' => '2025-11-05',
                'start_time' => '08:00',
                'end_time' => '13:00',
                'facility_fee' => $facilityFee,
                'amenities_fee' => 0,
                'total_fee' => 0,
                'status' => 'confirmed',
                'event_type' => 'Birthday',
                'guest_count' => 50,
                'reservation_token' => $reservationToken,
                'digital_signature' => $digitalSignature,
                'payment_id' => null,
            ]);

            // Insert amenities for this reservation
            $chair = DB::table('amenities')->where('name', 'Chair')->first();
            $table = DB::table('amenities')->where('name', 'Table')->first();

            $amenitiesTotal = 0;

            if ($chair) {
                DB::table('reservation_amenities')->insert([
                    'reservation_id' => $reservation->id,
                    'amenity_id' => $chair->id,
                    'quantity' => 50,
                    'price' => $chair->price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $amenitiesTotal += 50 * $chair->price;
            }

            if ($table) {
                DB::table('reservation_amenities')->insert([
                    'reservation_id' => $reservation->id,
                    'amenity_id' => $table->id,
                    'quantity' => 10,
                    'price' => $table->price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $amenitiesTotal += 10 * $table->price;
            }

            // Update reservation total fee
            $reservation->update([
                'amenities_fee' => $amenitiesTotal,
                'total_fee' => $facilityFee + $amenitiesTotal,
            ]);
        }
    }
}
