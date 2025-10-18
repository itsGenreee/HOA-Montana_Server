<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class FacilityController extends Controller
{
    //For Tennis Court
    public function availability1($id, $date, Request $request)
    {
        try {
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
            $discountedFee = optional($facility->fees->first())->discounted_fee ?? 100;

            $slots = [];
            $start    = Carbon::parse($facility->start_time);
            $end      = Carbon::parse($facility->end_time);
            $interval = (int) $facility->interval_minutes;

            while ($start < $end) {
                $slotStart = $start->copy();
                $slotEnd   = $slotStart->copy()->addMinutes($interval);

                // Find an overlapping reservation (if any) - USE RAW TIMES
                $reservation = $reservations->first(function ($res) use ($slotStart, $slotEnd) {
                    // Use getRawOriginal to get the raw database values
                    $resStart = Carbon::parse($res->getRawOriginal('start_time'));
                    $resEnd   = Carbon::parse($res->getRawOriginal('end_time'));
                    return $resStart < $slotEnd && $resEnd > $slotStart;
                });

                $slots[] = [
                    'start_time' => $slotStart->format('g:i A'),
                    'end_time'   => $slotEnd->format('g:i A'),
                    'fee'        => $reservation ? $reservation->total_fee : $baseFee,
                    'discounted_fee' => $reservation ? $reservation->facility_fee : $discountedFee,
                    'available'  => !$reservation,
                    'user'       => $reservation && $reservation->user_id ? $reservation->user : null,
                    'customer_name' => $reservation ? $reservation->customer_name : null,
                    'id' => $reservation ? $reservation->id : null,
                ];

                $start->addMinutes($interval);
            }

            return response()->json($slots);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    //For Basketball Court
    public function availability2($id, $date, Request $request)
    {
        try {
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

                // Check for overlapping reservations - USE RAW TIMES
                $reservation = $reservations->first(function ($res) use ($slotStart, $slotEnd) {
                    // Use getRawOriginal to get the raw database values
                    $resStart = Carbon::parse($res->getRawOriginal('start_time'));
                    $resEnd = Carbon::parse($res->getRawOriginal('end_time'));
                    return $resStart < $slotEnd && $resEnd > $slotStart;
                });

                $discountFee = $slotFee ? $slotFee->discounted_fee : 0;

                $slots[] = [
                    'start_time' => $slotStart->format('g:i A'),
                    'end_time'   => $slotEnd->format('g:i A'),
                    'fee'        => $reservation ? $reservation->total_fee : ($slotFee->fee ?? 0), // Use facility_fee
                    'discounted_fee' => $reservation ? $reservation->facility_fee : $discountFee,
                    'available'  => !$reservation,
                    'user'       => $reservation ? $reservation->user : null,
                    'customer_name' => $reservation ? $reservation->customer_name : null, // Added customer_name
                    'id' => $reservation ? $reservation->id : null,
                ];

                $start->addMinutes($interval);
            }

            return response()->json($slots);

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    //For Event Place
    public function availability3($id, $date, Request $request)
    {
        try {
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

                // Check if this block is reserved - USE RAW TIMES
                $reservation = $reservations->first(function ($res) use ($feeStart, $feeEnd) {
                    // Use getRawOriginal to get the raw database values
                    $resStart = Carbon::parse($res->getRawOriginal('start_time'));
                    $resEnd   = Carbon::parse($res->getRawOriginal('end_time'));
                    return $resStart < $feeEnd && $resEnd > $feeStart;
                });

                $slots[] = [
                    'start_time' => $feeStart->format('g:i A'),
                    'end_time'   => $feeEnd->format('g:i A'),
                    'fee'        => $reservation ? $reservation->total_fee : $feeEntry->fee, // Use facility_fee
                    'discounted_fee' => $reservation ? $reservation->facility_fee : $feeEntry->discounted_fee,
                    'available'  => !$reservation,
                    'user'       => $reservation ? $reservation->user : null,
                    'customer_name' => $reservation ? $reservation->customer_name : null, // Added customer_name
                    'id' => $reservation ? $reservation->id : null,
                ];
            }

            return response()->json($slots);

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    //Change Fee for Tennis Court
    public function changeFee1($id, Request $request) {
        if (!$id) {
            return response()->json(['error' => 'Facility is Required'], 400);
        }

        // Validate the request
        $request->validate([
            'fee' => 'required|numeric|min:0'
        ]);

        // Load facility with its fees
        $facility = Facility::with('fees')->findOrFail($id);

        // Find the first fee record and update it
        $feeRecord = $facility->fees->first();

        if (!$feeRecord) {
            return response()->json(['error' => 'No fee record found for this facility'], 400);
        }

        // Simply update the fee
        $feeRecord->update([
            'fee' => $request->fee
        ]);

        return response()->json([
            'message' => $facility->name . ' fee updated successfully',
            'fee' => $request->fee
        ]);
    }

    public function changeFee2($id, Request $request) {
    if (!$id) {
        return response()->json(['error' => 'Facility is Required'], 400);
    }

    // Validate the request
    $request->validate([
        'name' => 'required|in:day,night', // Must be 'day' or 'night'
        'fee' => 'required|numeric|min:0'
    ]);

    // Load facility with its fees
    $facility = Facility::with('fees')->findOrFail($id);

    // Find the specific shift fee by type AND name
    $shiftFee = $facility->fees()
        ->where('type', 'shift')
        ->where('name', $request->name)
        ->first();

    if (!$shiftFee) {
        return response()->json(['error' => $request->name . ' shift fee not found for this facility'], 400);
    }

    // Update the specific shift fee
    $shiftFee->update([
        'fee' => $request->fee
    ]);

    return response()->json([
        'message' => $facility->name . ' ' . $request->name . ' shift fee updated successfully',
        'fee' => $request->fee
    ]);
    }

    public function changeFee3($id, Request $request) {
    if (!$id) {
        return response()->json(['error' => 'Facility is Required'], 400);
    }

    // Validate the request
    $request->validate([
        'name' => 'required|in:Morning Event,Afternoon Event', // Must be specific block name
        'fee' => 'required|numeric|min:0'
    ]);

    // Load facility with its fees
    $facility = Facility::with('fees')->findOrFail($id);

    // Find the specific block fee by name
    $blockFee = $facility->fees()->where('name', $request->name)->first();

    if (!$blockFee) {
        return response()->json(['error' => $request->name . ' not found for this facility'], 400);
    }

    // Update the specific block fee
    $blockFee->update([
        'fee' => $request->fee
    ]);

    return response()->json([
        'message' => $facility->name . ' ' . $request->name . ' fee updated successfully',
        'fee' => $request->fee
    ]);
    }
}
