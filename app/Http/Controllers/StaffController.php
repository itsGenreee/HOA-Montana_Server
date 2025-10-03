<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Helpers\DigitalSignature;

class StaffController extends Controller
{
    public function verifyReservation(Request $request)
    {
        try {
            // 👇 ONLY validate reservation_token and digital_signature
            $validated = $request->validate([
                'reservation_token' => 'required|string',
                'digital_signature' => 'required|string',
            ]);


            // 👇 Find reservation by reservation_token ONLY
            $reservation = Reservation::where('reservation_token', $validated['reservation_token'])
                ->with(['user', 'facility']) // Load relationships for display
                ->first();

            if (!$reservation) {
                return response()->json([
                    'is_valid' => false,
                    'message' => 'Reservation not found'
                ], 404);
            }

            // 👇 Verify ONLY the reservation_token with the digital_signature
            $isValid = DigitalSignature::verify($validated['reservation_token'], $validated['digital_signature']);

            if (!$isValid) {
                return response()->json([
                    'is_valid' => false,
                    'message' => 'Digital signature verification failed - QR code may be tampered with'
                ], 400);
            }

            // Check reservation status
            if ($reservation->status !== 'confirmed') {
                return response()->json([
                    'is_valid' => false,
                    'message' => 'Reservation is not confirmed. Current status: ' . $reservation->status
                ]);
            }

            // Check if reservation time is valid
            $reservationDateTime = $reservation->date . ' ' . $reservation->start_time;
            $reservationTime = strtotime($reservationDateTime);
            $currentTime = time();

            // Allow check-in 15 minutes before start time
            if ($reservationTime > ($currentTime + 900)) {
                return response()->json([
                    'is_valid' => false,
                    'message' => 'Too early for check-in (15 minutes before start time)'
                ]);
            }

            // if ($reservationTime < ($currentTime - 3600)) {
            //     return response()->json([
            //         'is_valid' => false,
            //         'message' => 'Reservation time has passed'
            //     ]);
            // }

            return response()->json([
                'is_valid' => true,
                'message' => 'Reservation verified successfully',
                'reservation' => $reservation,
                'user' => $reservation->user,
                'facility' => $reservation->facility
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'is_valid' => false,
                'message' => 'Verification failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // checkIn method remains the same (already simplified)
    public function checkIn(Request $request)
    {
        try {
            $validated = $request->validate([
                'reservation_token' => 'required|string',
            ]);

            $reservation = Reservation::where('reservation_token', $validated['reservation_token'])->first();

            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservation not found'
                ], 404);
            }

            // Update reservation status
            $reservation->update([
                'status' => 'checked_in',
                'checked_in_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check-in successful',
                'reservation' => $reservation
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Check-in failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
