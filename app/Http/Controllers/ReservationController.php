<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\DigitalSignature;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    /**
     * List all reservations (admin or for user dashboard)
     */
    public function index()
    {
        $userId = Auth::id(); // Get the authenticated user's ID
        $reservations = Reservation::where('user_id', $userId)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return response()->json($reservations);
    }

    /**
     * Create a new reservation
     */
public function store(Request $request)
{
    // Validate incoming request
    $validated = $request->validate([
        'facility_id' => 'required|exists:facilities,id',
        'date'        => 'required|date',
        'start_time'  => 'required|date_format:H:i',
        'end_time'    => 'required|date_format:H:i',
        'fee'         => 'nullable|numeric',
    ]);

    try {
        // Generate reservation token
        $reservationToken = Str::uuid()->toString();

        // Sign the reservation token
        $digitalSignature = DigitalSignature::sign($reservationToken);

        // Create reservation
        $reservation = Reservation::create([
            'user_id'           => Auth::id(),
            'facility_id'       => $validated['facility_id'],
            'date'              => $validated['date'],
            'start_time'        => $validated['start_time'],
            'end_time'          => $validated['end_time'],
            'fee'               => $validated['fee'] ?? 100,
            'status'            => 'pending',
            'reservation_token' => $reservationToken,
            'digital_signature' => $digitalSignature, // Store the signature
            'payment_id'        => null,
        ]);

        return response()->json([
            'message' => 'Reservation created and signed successfully',
            'reservation' => $reservation
        ], 201);

    } catch (\Exception $e) {
        // Catch any errors (like DB issues, key errors, or mass assignment)
        return response()->json([
            'message' => 'Failed to create reservation',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    /**
     * Verify reservation signature
     */
    public function verify($id)
    {
        $reservation = Reservation::findOrFail($id);

        $isValid = DigitalSignature::verify(
            $reservation->reservation_token,
            $reservation->digital_signature
        );

        return response()->json([
            'valid' => $isValid,
            'reservation' => $reservation
        ]);
    }

    /**
     * Show a single reservation
     */
    public function show($id)
    {
        $reservation = Reservation::findOrFail($id);
        return response()->json($reservation);
    }

    /**
     * Update a reservation (for example, admin can confirm or cancel)
     */
    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,confirmed,canceled',
            'fee' => 'nullable|numeric',
        ]);

        $reservation->update($request->only(['status', 'fee']));

        return response()->json([
            'message' => 'Reservation updated successfully',
            'reservation' => $reservation,
        ]);
    }

    /**
     * Delete a reservation
     */
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return response()->json([
            'message' => 'Reservation deleted successfully',
        ]);
    }
}
