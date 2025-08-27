<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
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
        $request->validate([
            'facility' => 'required|string',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',  // matches SQL TIME format
            'end_time' => 'required|date_format:H:i|after:start_time',
            'fee' => 'nullable|numeric',
        ]);

        // Bind user_id automatically from the authenticated user
        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'facility' => $request->facility,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'fee' => $request->fee,
            'status' => 'pending', // default status
        ]);

        return response()->json([
            'message' => 'Reservation created successfully',
            'reservation' => $reservation,
        ], 201);
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
