<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Facility;
use App\Models\FacilityFee;
use App\Models\Reservation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
    $userId = Auth::id();

    $reservations = Reservation::with('facility:id,name') // eager load only id & name
        ->where('user_id', $userId)
        ->orderBy('date')
        ->orderBy('start_time')
        ->get()
        ->map(function ($reservation) {
            // Ensure we return formatted strings (not Carbon objects)
            $date = $reservation->date ? ($reservation->date instanceof Carbon
                ? $reservation->date->format('Y-m-d')
                : Carbon::parse($reservation->date)->format('Y-m-d')) : null;

            $start = $reservation->start_time ? (
                $reservation->start_time instanceof Carbon
                    ? $reservation->start_time->format('H:i')
                    : Carbon::parse($reservation->start_time)->format('H:i')
            ) : null;

            $end = $reservation->end_time ? (
                $reservation->end_time instanceof Carbon
                    ? $reservation->end_time->format('H:i')
                    : Carbon::parse($reservation->end_time)->format('H:i')
            ) : null;

            return [
                'id' => $reservation->id,
                'facility_id' => $reservation->facility_id,
                'facility' => $reservation->facility ? $reservation->facility->name : null,
                'date' => $date,
                'start_time' => $start,
                'end_time' => $end,
                'status' => $reservation->status,
                'reservation_token' => $reservation->reservation_token,
                'digital_signature' => $reservation->digital_signature,
            ];
        });

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
        'guest_count' => 'nullable|integer',
        'event_type'  => 'nullable|string|max:255',
        'amenities'   => 'nullable|array',
        'amenities.*.amenity_id' => 'required|exists:amenities,id',
        'amenities.*.quantity'   => 'required|integer|min:0',
    ]);

    try {
        // Generate reservation token
        $reservationToken = Str::uuid()->toString();

        // Sign the reservation token
        $dataToSign = json_encode([
            'user_id'     => Auth::id(),
            'facility_id' => $validated['facility_id'],
            'date'        => $validated['date'],
            'start_time'  => $validated['start_time'],
            'end_time'    => $validated['end_time'],
            'reservation_token' => $reservationToken,
        ], JSON_UNESCAPED_SLASHES);

        $digitalSignature = DigitalSignature::sign($dataToSign);

        // Calculate fees separately
        $facility = Facility::find($validated['facility_id']);

        if (!$facility) {
            return response()->json(['message' => 'Facility not found'], 404);
        }

        // Get the most recent fee with fallback
        $latestFee = $facility->fees()->latest()->first();
        $facilityFee = $latestFee ? $latestFee->fee : 100;

        $amenitiesFee = 0;

        // Process amenities if provided
        if (!empty($validated['amenities'])) {
            foreach ($validated['amenities'] as $amenityItem) {
                $amenity = Amenity::find($amenityItem['amenity_id']);

                if ($amenity && $amenityItem['quantity'] > 0) {
                    // Check quantity limits
                    if ($amenity->max_quantity !== null && $amenityItem['quantity'] > $amenity->max_quantity) {
                        throw new \Exception("Quantity for {$amenity->name} exceeds maximum allowed quantity of {$amenity->max_quantity}");
                    }

                    // Calculate amenity cost using the CURRENT price (snapshot)
                    $amenityCost = $amenity->price * $amenityItem['quantity'];
                    $amenitiesFee += $amenityCost;
                }
            }
        }

        $totalFee = $facilityFee + $amenitiesFee;

        // Create reservation
        $reservation = Reservation::create([
            'user_id'           => Auth::id(),
            'facility_id'       => $validated['facility_id'],
            'date'              => $validated['date'],
            'start_time'        => $validated['start_time'],
            'end_time'          => $validated['end_time'],
            'facility_fee'      => $facilityFee,
            'amenities_fee'     => $amenitiesFee,
            'total_fee'         => $totalFee,
            'status'            => 'pending',
            'event_type'        => $validated['event_type'] ?? null,
            'guest_count'       => $validated['guest_count'] ?? null,
            'reservation_token' => $reservationToken,
            'digital_signature' => $digitalSignature,
            'payment_id'        => null,
        ]);

        // Attach amenities to reservation with PRICE SNAPSHOT
        if (!empty($validated['amenities'])) {
            foreach ($validated['amenities'] as $amenityItem) {
                $amenity = Amenity::find($amenityItem['amenity_id']);

                if ($amenity && $amenityItem['quantity'] > 0) {
                    // Store the INDIVIDUAL UNIT PRICE (not total) as snapshot
                    $reservation->amenities()->attach($amenityItem['amenity_id'], [
                        'quantity' => $amenityItem['quantity'],
                        'price'    => $amenity->price, // ← UNIT PRICE SNAPSHOT
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Load amenities relationship for response
        $reservation->load('amenities');

        return response()->json([
            'message' => 'Reservation created and signed successfully',
            'reservation' => $reservation,
            'breakdown' => [
                'facility_fee' => $facilityFee,
                'amenities_fee' => $amenitiesFee,
                'total_fee' => $totalFee
            ]
        ], 201);

    } catch (\Exception $e) {
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
