<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use Carbon\Carbon;

class CancelExpiredReservations extends Command
{
    protected $signature = 'reservations:cancel-expired';
    protected $description = 'Automatically cancel reservations that passed their payment deadline';

public function handle()
{
    $now = Carbon::now()->format('Y-m-d H:i:s');

    $expiredReservations = Reservation::where('status', 'pending')
        ->where('payment_deadline', '<=', $now)
        ->get();

    $count = 0;
    foreach ($expiredReservations as $reservation) {
        $reservation->update([
            'status' => 'canceled',
            'cancelled_at' => now(),
            'cancellation_reason' => 'Automatically cancelled: Reservation time started without payment'
        ]);
        $count++;

        $this->info("Cancelled reservation ID: {$reservation->id} for {$reservation->date} {$reservation->start_time}");
    }

    $this->info("Successfully cancelled {$count} expired reservations.");
    return 0; // Success
}
}
