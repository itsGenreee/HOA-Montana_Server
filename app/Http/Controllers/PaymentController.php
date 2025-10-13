<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Luigel\Paymongo\Facades\Paymongo;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Create a payment intent for a reservation
     */
    public function createPaymentIntent(Request $request): JsonResponse
    {
        $request->validate([
            'reservation_token' => 'required|string',
            'amount' => 'required|numeric|min:1'
        ]);

        try {
            $reservation = Reservation::where('reservation_token', $request->reservation_token)->first();

            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservation not found'
                ], 404);
            }

            // ✅ CORRECT: Use the facade directly
            $paymentIntent = Paymongo::paymentIntent()->create([
                'amount' => $request->amount * 100,
                'currency' => 'PHP',
                'payment_method_allowed' => ['gcash'],
                'description' => "Facility Reservation #{$reservation->id}",
                'metadata' => [
                    'reservation_token' => $reservation->reservation_token,
                    'reservation_id' => (string) $reservation->id, // Convert to string
                    'facility_name' => $reservation->facility->name ?? 'Unknown Facility'
                ]
            ]);

            // Store payment intent ID in reservation
            $reservation->update([
                'payment_intent_id' => $paymentIntent->id,
                'payment_status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'client_key' => $paymentIntent->client_key,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $request->amount
            ]);

        } catch (\Exception $e) {
            Log::error('Payment intent creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment intent',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Attach payment method and confirm payment
     */
    public function confirmPayment(Request $request): JsonResponse
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'payment_method_id' => 'required|string'
        ]);

        try {
            // Find reservation by payment intent ID
            $reservation = Reservation::where('payment_intent_id', $request->payment_intent_id)->first();

            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservation not found'
                ], 404);
            }

            // ✅ CORRECT: Use facade for finding and attaching
            $paymentIntent = Paymongo::paymentIntent()->find($request->payment_intent_id);
            $paymentIntent->attach($request->payment_method_id);

            // Note: Actual payment confirmation happens via webhook
            $reservation->update([
                'payment_status' => 'processing'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment processing started',
                'reservation_status' => $reservation->status,
                'payment_status' => 'processing'
            ]);

        } catch (\Exception $e) {
            Log::error('Payment confirmation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Payment failed to process',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle PayMongo webhooks for payment events
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->all();
        Log::info('Webhook received:', $payload);

        try {
            $eventType = $payload['data']['attributes']['type'] ?? null;
            $paymentData = $payload['data']['attributes']['data'] ?? null;

            if (!$eventType || !$paymentData) {
                return response()->json(['error' => 'Invalid webhook payload'], 400);
            }

            $paymentIntentId = $paymentData['attributes']['payment_intent_id'] ?? null;

            if (!$paymentIntentId) {
                return response()->json(['error' => 'No payment intent ID found'], 400);
            }

            // Find reservation by payment intent ID
            $reservation = Reservation::where('payment_intent_id', $paymentIntentId)->first();

            if (!$reservation) {
                return response()->json(['error' => 'Reservation not found'], 404);
            }

            // Handle different event types
            switch ($eventType) {
                case 'payment.paid':
                    $reservation->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed',
                        'paid_at' => now()
                    ]);
                    break;

                case 'payment.failed':
                    $reservation->update([
                        'payment_status' => 'failed',
                        'status' => 'pending'
                    ]);
                    break;

                default:
                    Log::info("Unhandled webhook event: {$eventType}");
            }

            Log::info("Webhook processed: {$eventType} for reservation #{$reservation->id}");

            // Always return 200 to acknowledge receipt
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Webhook processing failed: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(Request $request): JsonResponse
    {
        $request->validate([
            'reservation_token' => 'required|string'
        ]);

        $reservation = Reservation::where('reservation_token', $request->reservation_token)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'payment_status' => $reservation->payment_status,
            'reservation_status' => $reservation->status,
            'paid_at' => $reservation->paid_at
        ]);
    }
}
