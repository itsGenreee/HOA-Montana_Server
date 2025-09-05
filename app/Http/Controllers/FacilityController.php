<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\Reservation;
use Carbon\Carbon;

class FacilityController extends Controller
{
public function availability($id, $date, Request $request)
{
    if (!$date) {
        return response()->json(['error' => 'Date is required'], 400);
    }

    // Get the facility, or fail if not found
    $facility = Facility::findOrFail($id);

    // Use the relationship to fetch confirmed reservations for that facility and date
    $reservations = $facility->reservations()
        ->whereDate('date', $date) // assuming it's a DATE column, not datetime
        ->where('status', 'confirmed')
        ->with('user') // eager load the user
        ->get();

    $slots = [];
    $start = Carbon::parse($facility->start_time);
    $end = Carbon::parse($facility->end_time);
    $interval = $facility->interval_minutes;

    while ($start < $end) {
        $slotStart = $start->copy();
        $slotEnd = $slotStart->copy()->addMinutes($interval);

        // Check if any confirmed reservation overlaps with this slot
        $reservation = $reservations->first(function ($res) use ($slotStart, $slotEnd) {
            $resStart = Carbon::parse($res->start_time);
            $resEnd = Carbon::parse($res->end_time);
            return $resStart < $slotEnd && $resEnd > $slotStart;
        });

        $slots[] = [

            'start_time' => $slotStart->format('H:i'),
            'end_time' => $slotEnd->format('H:i'),
            'fee' => $reservation ? $reservation->fee : 100,
            'available' => !$reservation,
            'user' => $reservation ? $reservation->user : null,
        ];

        $start->addMinutes($interval);
    }

    return response()->json($slots);
}






}
