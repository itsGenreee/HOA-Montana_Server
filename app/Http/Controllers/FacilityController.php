<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use Carbon\Carbon;

class FacilityController extends Controller
{
    public function availability1($id, $date, Request $request)
    {
        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        // Load facility with its fees
        $facility = Facility::with('fees')->findOrFail($id);

        // Get the facility's confirmed reservations for the given date
        $reservations = $facility->reservations()
            ->whereDate('date', $date)
            ->where('status', 'confirmed')
            ->with('user')
            ->get();

        // Pick a fee to use for open slots (simplest: first fee row)
        $baseFee = optional($facility->fees->first())->fee ?? 100;

        $slots = [];
        $start    = Carbon::parse($facility->start_time);
        $end      = Carbon::parse($facility->end_time);
        $interval = (int) $facility->interval_minutes;

        while ($start < $end) {
            $slotStart = $start->copy();
            $slotEnd   = $slotStart->copy()->addMinutes($interval);

            // Find an overlapping reservation (if any)
            $reservation = $reservations->first(function ($res) use ($slotStart, $slotEnd) {
                $resStart = Carbon::parse($res->start_time);
                $resEnd   = Carbon::parse($res->end_time);
                return $resStart < $slotEnd && $resEnd > $slotStart;
            });

            $slots[] = [
                'start_time' => $slotStart->format('g:i A'),
                'end_time'   => $slotEnd->format('g:i A'),
                // If you want fee to ALWAYS come from facility_fees, use $baseFee:
                'fee'        => $baseFee,
                'available'  => !$reservation,
                'user'       => $reservation ? $reservation->user : null,
            ];

            $start->addMinutes($interval);
        }

        return response()->json($slots);
    }

    public function availability2($id, $date, Request $request)
        {
        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        // Load facility with fees
        $facility = Facility::with('fees')->findOrFail($id);

        // Get confirmed reservations for the facility on the given date
        $reservations = $facility->reservations()
            ->whereDate('date', $date)
            ->where('status', 'confirmed')
            ->with('user')
            ->get();

        // Get facility fees, sorted by start_time (or name if you prefer)
        $fees = $facility->fees->sortBy('start_time');

        $slots = [];
        $start = Carbon::parse($facility->start_time);
        $end = Carbon::parse($facility->end_time);
        $interval = $facility->interval_minutes;

        while ($start < $end) {
            $slotStart = $start->copy();
            $slotEnd = $slotStart->copy()->addMinutes($interval);

            // Match slot with correct fee based on time range
            $slotFee = $fees->first(function ($fee) use ($slotStart) {
                $feeStart = Carbon::parse($fee->start_time);
                $feeEnd = Carbon::parse($fee->end_time);
                return $slotStart->between($feeStart, $feeEnd->subMinute());
            });

            // Check for overlapping reservations
            $reservation = $reservations->first(function ($res) use ($slotStart, $slotEnd) {
                $resStart = Carbon::parse($res->start_time);
                $resEnd = Carbon::parse($res->end_time);
                return $resStart < $slotEnd && $resEnd > $slotStart;
            });

            $slots[] = [
                'start_time' => $slotStart->format('g:i A'),
                'end_time'   => $slotEnd->format('g:i A'),
                'fee'        => $reservation ? $reservation->fee : ($slotFee->fee ?? 0),
                'available'  => !$reservation,
                'user'       => $reservation ? $reservation->user : null,
            ];

            $start->addMinutes($interval);
        }

        return response()->json($slots);
    }

    public function availability3($id, $date, Request $request)
{
    if (!$date) {
        return response()->json(['error' => 'Date is required'], 400);
    }

    // Get the facility and its block fees
    $facility = Facility::with('fees')->findOrFail($id);

    // Get confirmed reservations for this date
    $reservations = $facility->reservations()
        ->whereDate('date', $date)
        ->where('status', 'confirmed')
        ->with('user')
        ->get();

    $slots = [];

    // Each fee entry is considered a block
    foreach ($facility->fees as $feeEntry) {
        $feeStart = Carbon::parse($feeEntry->start_time);
        $feeEnd   = Carbon::parse($feeEntry->end_time);

        // Check if this block is reserved
        $reservation = $reservations->first(function ($res) use ($feeStart, $feeEnd) {
            $resStart = Carbon::parse($res->start_time);
            $resEnd   = Carbon::parse($res->end_time);
            return $resStart < $feeEnd && $resEnd > $feeStart;
        });

        $slots[] = [
            'start_time' => $feeStart->format('g:i A'),
            'end_time'   => $feeEnd->format('g:i A'),
            'fee'        => $feeEntry->fee,  // taken directly from facility_fees
            'available'  => !$reservation,
            'user'       => $reservation ? $reservation->user : null,
        ];
    }

    return response()->json($slots);
}

}
