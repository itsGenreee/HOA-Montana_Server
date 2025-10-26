<?php

namespace App\Http\Controllers;

use App\Models\User;
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
            // Calculate payment deadline (reservation start datetime)
            $paymentDeadline = Carbon::createFromFormat('Y-m-d H:i', "{$validated['date']} {$validated['start_time']}");

            // Check if payment deadline is in the past
            if ($paymentDeadline->isPast()) {
                return response()->json([
                    'message' => 'Cannot create reservation for past time slots',
                    'errors' => [
                        'date' => ['Cannot create reservation for past time slots']
                    ]
                ], 422); // Use 422 for validation errors
            }

            // Check for conflicting CONFIRMED reservations
            $conflictingConfirmed = Reservation::where('facility_id', $validated['facility_id'])
                ->where('date', $validated['date'])
                ->where('status', 'confirmed')
                ->where(function($query) use ($validated) {
                $query->where(function($q) use ($validated) {
                    $q->where('start_time', '<', $validated['end_time'])
                    ->where('end_time', '>', $validated['start_time']);
                        });
                })->exists();

            if ($conflictingConfirmed) {
                return response()->json([
                    'message' => 'This time slot is already booked by another user.'
                ], 400);
            }

            // Check for existing PENDING reservations (multiple reservations allowed, but warn user)
            $conflictingPendingCount = Reservation::where('facility_id', $validated['facility_id'])
                ->where('date', $validated['date'])
                ->where('status', 'pending')
                ->where(function($query) use ($validated) {
                    $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                        ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                        ->orWhere(function($q) use ($validated) {
                            $q->where('start_time', '<=', $validated['start_time'])
                                ->where('end_time', '>=', $validated['end_time']);
                        });
                })
                ->count();

            // Generate reservation token
            $reservationToken = Str::uuid()->toString();
            $digitalSignature = DigitalSignature::sign($reservationToken);

            $user = Auth::user();
            $facility = Facility::find($validated['facility_id']);
            $latestFee = $facility->fees()->latest()->first();

            // Apply discount if user is verified - use discounted_fee for verified users
            if ($user->status == User::STATUS_VERIFIED && $latestFee && $latestFee->discounted_fee) {
                $facilityFee = $latestFee->discounted_fee;
                $isDiscounted = true;
            } else {
                $facilityFee = $latestFee ? $latestFee->fee : 100;
                $isDiscounted = false;
            }

            $amenitiesFee = 0;
            if (!empty($validated['amenities'])) {
                foreach ($validated['amenities'] as $amenityItem) {
                    $amenity = Amenity::find($amenityItem['amenity_id']);
                    if ($amenity && $amenityItem['quantity'] > 0) {
                        $amenityCost = $amenity->price * $amenityItem['quantity'];
                        $amenitiesFee += $amenityCost;
                    }
                }
            }

            $totalFee = $facilityFee + $amenitiesFee;

            // Create reservation with payment deadline
            $reservation = Reservation::create([
                'user_id'           => $user->id,
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
                'payment_deadline'  => $paymentDeadline,
            ]);

            // Attach amenities to reservation
            if (!empty($validated['amenities'])) {
                foreach ($validated['amenities'] as $amenityItem) {
                    $amenity = Amenity::find($amenityItem['amenity_id']);
                    if ($amenity && $amenityItem['quantity'] > 0) {
                        $reservation->amenities()->attach($amenityItem['amenity_id'], [
                            'quantity' => $amenityItem['quantity'],
                            'price'    => $amenity->price,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            $reservation->load('amenities');

            $response = [
                'message' => 'Reservation created successfully',
                'reservation' => $reservation,
                'payment_deadline' => $paymentDeadline->format('Y-m-d H:i:s'),
                'breakdown' => [
                    'facility_fee' => $facilityFee,
                    'amenities_fee' => $amenitiesFee,
                    'total_fee' => $totalFee,
                    'discount_applied' => $isDiscounted,
                    'original_facility_fee' => $latestFee ? $latestFee->fee : 100,
                    'discounted_fee' => $latestFee ? $latestFee->discounted_fee : null,
                ]
            ];

            // Warn user about conflicting pending reservations
            if ($conflictingPendingCount > 0) {
                $response['warning'] = "There are {$conflictingPendingCount} other pending reservation(s) for this time slot. Only the first one to pay will be confirmed.";
            }

            return response()->json($response, 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create reservation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

        // In your ReservationController.php
public function storeByStaff(Request $request)
{
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
        'customer_name'  => 'required|string|max:255',
        'customer_phone' => 'nullable|string|max:20',
        'customer_email' => 'nullable|email|max:255',
        'customer_verified' => 'nullable|boolean',
        'facility_fee' => 'required|numeric', // Use the fee sent from C#
    ]);

    try {
        // Calculate payment deadline
        $paymentDeadline = Carbon::createFromFormat('Y-m-d H:i', "{$validated['date']} {$validated['start_time']}");

        if ($paymentDeadline->isPast()) {
            return response()->json([
                'message' => 'Cannot create reservation for past time slots'
            ], 400);
        }

        // Check for conflicting CONFIRMED reservations
        $conflictingConfirmed = Reservation::where('facility_id', $validated['facility_id'])
            ->where('date', $validated['date'])
            ->where('status', 'confirmed')
            ->where(function($query) use ($validated) {
                $query->where(function($q) use ($validated) {
                    $q->where('start_time', '<', $validated['end_time'])
                    ->where('end_time', '>', $validated['start_time']);
                });
            })
            ->exists();

        if ($conflictingConfirmed) {
            return response()->json([
                'message' => 'This time slot is already booked by another user.'
            ], 400);
        }

        // USE THE FACILITY FEE SENT FROM C# - don't recalculate
        $facilityFee = $validated['facility_fee'];

        $amenitiesFee = 0;
        if (!empty($validated['amenities'])) {
            foreach ($validated['amenities'] as $amenityItem) {
                $amenity = Amenity::find($amenityItem['amenity_id']);
                if ($amenity && $amenityItem['quantity'] > 0) {
                    $amenityCost = $amenity->price * $amenityItem['quantity'];
                    $amenitiesFee += $amenityCost;
                }
            }
        }

        $totalFee = $facilityFee + $amenitiesFee;

        // Generate reservation token and signature
        $reservationToken = Str::uuid()->toString();
        $digitalSignature = DigitalSignature::sign($reservationToken);

        // Create reservation with customer info instead of user_id
        $reservation = Reservation::create([
            'user_id'           => null, // No user account linked
            'facility_id'       => $validated['facility_id'],
            'date'              => $validated['date'],
            'start_time'        => $validated['start_time'],
            'end_time'          => $validated['end_time'],
            'facility_fee'      => $facilityFee, // This will be 7000 when discount is applied
            'amenities_fee'     => $amenitiesFee,
            'total_fee'         => $totalFee,
            'status'            => 'confirmed', // Auto-confirm for staff reservations
            'event_type'        => $validated['event_type'] ?? null,
            'guest_count'       => $validated['guest_count'] ?? null,
            'reservation_token' => $reservationToken,
            'digital_signature' => $digitalSignature,
            'payment_id'        => 'CASH', // Mark as cash payment
            'payment_deadline'  => $paymentDeadline,
            'customer_name'     => $validated['customer_name'],
            'customer_phone'    => $validated['customer_phone'] ?? null,
            'customer_email'    => $validated['customer_email'] ?? null,
            'confirmed_at'      => now(), // Auto-confirm
            'confirmed_by'      => Auth::id(), // Staff who made the reservation
        ]);

        // CANCEL CONFLICTING PENDING RESERVATIONS - ADDED THIS SECTION
        $staff = Auth::user();
        $cancelledCount = Reservation::where('facility_id', $validated['facility_id'])
            ->where('date', $validated['date'])
            ->where('status', 'pending')
            ->where('id', '!=', $reservation->id)
            ->where(function($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhere(function($q) use ($validated) {
                        $q->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                    });
            })
            ->update([
                'status' => 'canceled',
                'cancelled_at' => now(),
                'cancelled_by' => $staff->id,
                'cancellation_reason' => 'Cancelled: Time slot confirmed for another reservation'
            ]);

        // Attach amenities
        if (!empty($validated['amenities'])) {
            foreach ($validated['amenities'] as $amenityItem) {
                $amenity = Amenity::find($amenityItem['amenity_id']);
                if ($amenity && $amenityItem['quantity'] > 0) {
                    $reservation->amenities()->attach($amenityItem['amenity_id'], [
                        'quantity' => $amenityItem['quantity'],
                        'price'    => $amenity->price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $reservation->load('amenities');

        return response()->json([
            'message' => 'Reservation created successfully',
            'reservation' => $reservation,
            'qr_code_data' => $reservationToken, // For QR code generation
            'cancelled_pending_reservations' => $cancelledCount, // Added this field
            'breakdown' => [
                'facility_fee' => $facilityFee,
                'amenities_fee' => $amenitiesFee,
                'total_fee' => $totalFee,
                'discount_applied' => ($validated['customer_verified'] ?? false) && $validated['facility_id'] == 3
            ]
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to create reservation',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    public function pendingReservationCount()
    {

        $pendingCount = Reservation::where('status', 'pending')->count();

        return response()->json([
            'pending_reservations' => $pendingCount
        ]);
    }

    public function getDashboardStats()
{
    $staffId = Auth::guard('staff')->user()->id;

    $stats = [
        'staff_checked_in_count' => Reservation::where('checked_in_by', $staffId)->count(),
        'today_reservation_count' => Reservation::where('date', today())
                                               ->where('status', 'confirmed')
                                               ->count(),
        'pending_reservation_count' => Reservation::where('status', 'pending')->count(),
        'total_reservation_count' => Reservation::count(),
    ];

    return response()->json([
        'success' => true,
        'data' => $stats
    ]);
    }

    public function staffCheckedInCount(){
        $staff = Auth::guard('staff')->user();

        $staffCheckedInCount = Reservation::where('checked_in_by', $staff->id)
            ->where('status', 'checked_in')
            ->count();


        return response()->json([
            'staff_checked_in_count' => $staffCheckedInCount
        ]);
    }

    public function todayReservationCount()
    {
        $today = Carbon::today()->toDateString();

        $todayCount = Reservation::where('date', $today)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->count();

        return response()->json([
            'today' => $today,
            'total_reservations_today' => $todayCount
        ]);
    }

    public function totalReservationCount()
    {
    $totalCount = Reservation::count();

    $breakdown = [
        'pending' => Reservation::where('status', 'pending')->count(),
        'confirmed' => Reservation::where('status', 'confirmed')->count(),
        'checked_in' => Reservation::where('status', 'checked_in')->count(),
        'canceled' => Reservation::where('status', 'canceled')->count(),
        'today_reservation' => Reservation::where('date', today())
                                               ->where('status', 'confirmed')
                                               ->count(),
    ];

    return response()->json([
        'total_reservations' => $totalCount,
        'breakdown' => $breakdown,
        ]);
    }
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
