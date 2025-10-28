<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Helpers\DigitalSignature;
use Illuminate\Support\Facades\Auth;

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

            // 👇 Verify the digital_signature FIRST before database query
            $isValid = DigitalSignature::verify($validated['reservation_token'], $validated['digital_signature']);

            if (!$isValid) {
                return response()->json([
                    'is_valid' => false,
                    'message' => 'Digital signature verification failed - QR code may be tampered'
                ], 400);
            }

            // 👇 ONLY query database if signature is valid
            $reservation = Reservation::where('reservation_token', $validated['reservation_token'])
                ->with(['user', 'facility']) // Load relationships for display
                ->first();

            if (!$reservation) {
                return response()->json([
                    'is_valid' => false,
                    'message' => 'Reservation not found'
                ], 404);
            }

            // Check reservation status
            if ($reservation->status !== 'confirmed') {
                if ($reservation->status === 'pending') {
                    return response()->json([
                        'is_valid' => false,
                        'message' => 'Reservation is still pending, pay to the HOA Montana Office first.'
                    ]);
                }

                if ($reservation->status === 'checked_in') {
                    return response()->json([
                        'is_valid' => false,
                        'message' => 'Reservation is already checked-in'
                    ]);
                }

                return response()->json([
                    'is_valid' => false,
                    'message' => 'Reservation is not confirmed. Current status: ' . $reservation->status
                ]);
            }

            // 👇 SIMPLE: Check if current time is past the end time
            $reservationEndDateTime = $reservation->date . ' ' . $reservation->end_time;
            $reservationEndTime = Carbon::parse($reservationEndDateTime);

            $currentTime = Carbon::now('Asia/Manila')->timestamp;

            if ($currentTime > $reservationEndTime) {
                return response()->json([
                    'is_valid' => false,
                    'message' => 'Reservation time has ended. End time was: ' . $reservation->end_time
                ]);
            }

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

            $staff = $request->user();

            // Update reservation status
            $reservation->update([
                'status' => 'checked_in',
                'checked_in_at' => now(),
                'checked_in_by' => $staff->id,
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

    public function getPendingReservations(Request $request)
    {
        try {
            $staff = Auth::guard('staff')->user();

            // Get pagination parameters from request
            $perPage = $request->get('per_page', 10); // Default to 10 items per page
            $page = $request->get('page', 1); // Default to page 1

            // Build the query with pagination
            $query = Reservation::with(['user', 'facility', 'amenities'])
                ->where('status', 'pending')
                ->orderBy('facility_id')
                ->orderBy('created_at') // First: who reserved first
                ->orderBy('date')       // Second: group by date
                ->orderBy('start_time'); // Third: group by start time

            // Get paginated results
            $paginator = $query->paginate($perPage, ['*'], 'page', $page);

            // Transform the data
            $transformedReservations = $paginator->getCollection()->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'user_name' => $reservation->user ? $reservation->user->first_name . ' ' . $reservation->user->last_name : null,
                    'user_email' => $reservation->user ? $reservation->user->email : null,
                    'user' => $reservation->user, // Include full user object
                    'facility_name' => $reservation->facility->name,
                    'date' => $reservation->date,
                    'start_time' => $reservation->start_time,
                    'end_time' => $reservation->end_time,
                    'total_fee' => $reservation->total_fee,
                    'event_type' => $reservation->event_type,
                    'guest_count' => $reservation->guest_count,
                    'payment_deadline' => $reservation->payment_deadline,
                    'created_at' => $reservation->created_at, // This shows who reserved first
                    'amenities' => $reservation->amenities->map(function ($amenity) {
                        return [
                            'name' => $amenity->name,
                            'quantity' => $amenity->pivot->quantity,
                            'price' => $amenity->pivot->price
                        ];
                    }),
                    'time_remaining' => $this->calculateTimeRemaining($reservation->payment_deadline),
                    'is_expiring_soon' => Carbon::now()->diffInMinutes($reservation->payment_deadline) < 60
                ];
            });

            // Replace the collection in the paginator with transformed data
            $paginator->setCollection($transformedReservations);

            return response()->json([
                'success' => true,
                'reservations' => $paginator->items(),
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'from' => $paginator->firstItem(),
                    'to' => $paginator->lastItem(),
                    'has_more_pages' => $paginator->hasMorePages(),
                    'next_page_url' => $paginator->nextPageUrl(),
                    'prev_page_url' => $paginator->previousPageUrl(),
                ],
                'staff' => [
                    'id' => $staff->id,
                    'first_name' => $staff->first_name,
                    'last_name' => $staff->last_name,
                    'role' => $staff->role
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pending reservations'
            ], 500);
        }
    }

    // NEW: Confirm reservation (admin function)
    public function confirmReservation($id, Request $request)
    {
        try {
            $staff = Auth::guard('staff')->user();
            $reservation = Reservation::with('facility')->findOrFail($id);

            // Validation checks
            if ($reservation->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => "Reservation is already {$reservation->status}."
                ], 400);
            }

            if (Carbon::now()->gt($reservation->payment_deadline)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot confirm expired reservation.'
                ], 400);
            }

            // Check for conflicting CONFIRMED reservations
            $conflictingConfirmed = $this->hasConflictingConfirmedReservations($reservation);
            if ($conflictingConfirmed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Time slot already taken by another confirmed reservation.'
                ], 400);
            }

            $reservation->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'confirmed_by' => $staff->id,
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            // Cancel all conflicting pending reservations with staff info
            $cancelledCount = $this->cancelConflictingPendingReservations($reservation, $staff);

            // Load updated relationships for response
            $reservation->load(['user', 'facility', 'amenities']);

            return response()->json([
                'success' => true,
                'message' => 'Reservation confirmed successfully.',
                'reservation' => $reservation,
                'cancelled_conflicts' => $cancelledCount,
                'confirmed_by' => $staff->first_name . ' ' . $staff->last_name
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm reservation: ' . $e->getMessage()
            ], 500);
        }
    }

    // NEW: Cancel reservation (admin function)
    public function cancelReservation($id, Request $request)
    {
        try {
            $staff = Auth::guard('staff')->user();
            $reservation = Reservation::findOrFail($id);
            $reason = $request->input('reason', 'Cancelled by admin');

            if ($reservation->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot cancel {$reservation->status} reservation."
                ], 400);
            }

            $reservation->update([
                'status' => 'canceled',
                'cancelled_at' => now(),
                'cancelled_by' => $staff->id,
                'cancellation_reason' => $reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reservation cancelled successfully.',
                'cancelled_by' => $staff->first_name . ' ' . $staff->last_name
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel reservation: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper methods
    private function hasConflictingConfirmedReservations(Reservation $reservation)
    {
        $date = $reservation->getRawOriginal('date');
        $startTime = $reservation->getRawOriginal('start_time');
        $endTime = $reservation->getRawOriginal('end_time');

        return $reservation->facility->reservations()
            ->where('date', $date)
            ->where('status', 'confirmed')
            ->where('id', '!=', $reservation->id)
            ->where(function($query) use ($startTime, $endTime) {
                $query->where(function($q) use ($startTime, $endTime) {
                    // Original reservation starts during existing reservation
                    $q->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
                });
            })
            ->exists();
    }

    private function cancelConflictingPendingReservations(Reservation $confirmedReservation, $staff)
    {
        $date = $confirmedReservation->getRawOriginal('date');
        $startTime = $confirmedReservation->getRawOriginal('start_time');
        $endTime = $confirmedReservation->getRawOriginal('end_time');

        return $confirmedReservation->facility->reservations()
            ->where('date', $date)
            ->where('status', 'pending')
            ->where('id', '!=', $confirmedReservation->id)
            ->where(function($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->update([
                'status' => 'canceled',
                'cancelled_at' => now(),
                'cancelled_by' => $staff->id,
                'cancellation_reason' => 'Cancelled: Time slot confirmed for another reservation'
            ]);
    }

    // temporary debug method to controller
    public function debugConflictingReservations($id)
    {
        try {
            $reservation = Reservation::with('facility')->findOrFail($id);

            // Use getRawOriginal() to get the raw database values, not the casted ones
            $date = $reservation->getRawOriginal('date'); // Gets '2025-11-25'
            $startTime = $reservation->getRawOriginal('start_time'); // Gets '10:00'
            $endTime = $reservation->getRawOriginal('end_time'); // Gets '11:00'

            $conflictingReservations = $reservation->facility->reservations()
                ->where('date', $date)
                ->where('status', 'pending')
                ->where('id', '!=', $reservation->id)
                ->where(function($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                        ->orWhereBetween('end_time', [$startTime, $endTime])
                        ->orWhere(function($q) use ($startTime, $endTime) {
                            $q->where('start_time', '<=', $startTime)
                                ->where('end_time', '>=', $endTime);
                        });
                })
                ->with(['user', 'facility'])
                ->get();

            return response()->json([
                'success' => true,
                'debug_info' => [
                    'reservation_being_confirmed' => [
                        'id' => $reservation->id,
                        'facility_id' => $reservation->facility_id,
                        'date_raw' => $date,
                        'start_time_raw' => $startTime,
                        'end_time_raw' => $endTime,
                        'date_casted' => $reservation->date, // Show what the cast gives us
                        'start_time_casted' => $reservation->start_time,
                        'end_time_casted' => $reservation->end_time,
                    ],
                    'conflicting_reservations_found' => $conflictingReservations->count(),
                    'conflicting_reservations' => $conflictingReservations->map(function ($conflict) {
                        return [
                            'id' => $conflict->id,
                            'user_name' => $conflict->user ? $conflict->user->name : 'N/A',
                            'date' => $conflict->getRawOriginal('date'),
                            'start_time' => $conflict->getRawOriginal('start_time'),
                            'end_time' => $conflict->getRawOriginal('end_time'),
                            'created_at' => $conflict->created_at,
                        ];
                    }),
                    'query_conditions' => [
                        'facility_id' => $reservation->facility_id,
                        'date' => $date,
                        'time_range' => $startTime . ' to ' . $endTime
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function calculateTimeRemaining($paymentDeadline)
    {
        $now = Carbon::now();
        $deadline = Carbon::parse($paymentDeadline);

        if ($now->gt($deadline)) {
            return 'Expired';
        }

        $diff = $now->diff($deadline);

        if ($diff->days > 0) {
            return "{$diff->days}d {$diff->h}h";
        } elseif ($diff->h > 0) {
            return "{$diff->h}h {$diff->i}m";
        } else {
            return "{$diff->i}m {$diff->s}s";
        }
    }

// In your StaffController or ReservationController
    public function cancelConfirmedReservation($id, Request $request)
    {
        try {
            $staff = Auth::guard('staff')->user();
            $reservation = Reservation::findOrFail($id);
            $reason = $request->input('reason', 'Cancelled by staff');

            // Validate it's a confirmed reservation
            if ($reservation->status !== 'confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot cancel {$reservation->status} reservation. Only confirmed reservations can be cancelled."
                ], 400);
            }

            // Check if reservation is in the past
            $reservationDate = $reservation->getRawOriginal('date');
            $reservationStartTime = $reservation->getRawOriginal('start_time');
            $reservationDateTime = Carbon::parse($reservationDate . ' ' . $reservationStartTime);

            if ($reservationDateTime->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel past reservations.'
                ], 400);
            }

            // Cancel the confirmed reservation
            $reservation->update([
                'status' => 'canceled',
                'cancelled_at' => now(),
                'cancelled_by' => $staff->id,
                'cancellation_reason' => $reason
            ]);

            // Handle payment refund if needed
            if ($reservation->payment_status === 'paid') {
                $reservation->update([
                    'payment_status' => 'refunded',
                    'cancellation_reason' => $reservation->cancellation_reason . ' (Payment refunded)'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Confirmed reservation cancelled successfully.',
                'reservation' => $reservation->load(['user', 'facility', 'amenities'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel confirmed reservation: ' . $e->getMessage()
            ], 500);
        }
    }
}
