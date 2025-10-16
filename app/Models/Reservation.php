<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'facility_id',
        'date',
        'start_time',
        'end_time',
        'facility_fee',
        'amenities_fee', // You were also missing this one
        'total_fee',
        'status',
        'confirmed_at',   // ← Add this
        'confirmed_by',   // ← Add this
        'cancelled_at',   // ← Add this if you use it in cancelReservation
        'cancellation_reason', // ← Add this if you use it
        'cancelled_by',
        'event_type',
        'guest_count',
        'customer_name',
        'customer_phone',
        'customer_email',
        'reservation_token',
        'digital_signature',
        'payment_id',
        'checked_in_at',
        'checked_in_by',
        'payment_intent_id',
        'payment_status',
        'paid_at',
        'payment_method',
        'payment_deadline', // ← Add this if you use it
        'payment_error',
        'payment_metadata'
    ];

    protected static function booted()
    {
        static::creating(function ($reservation) {
            if (!$reservation->reservation_token) {
                $reservation->reservation_token = Str::uuid(); // Generate unique token
            }
        });
    }

    /**
     * Define the relationship to the user who made the reservation
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function reservationAmenities()
    {
        return $this->hasMany(ReservationAmenity::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'reservation_amenities')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

    public function checkedInBy()
    {
        return $this->belongsTo(Staff::class, 'checked_in_by');
    }

    // Add this relationship for confirmed_by
    public function confirmedBy()
    {
        return $this->belongsTo(Staff::class, 'confirmed_by');
    }

    /**
     * Optional: Casts for date/time and money
     */
    protected $casts = [
        'date' => 'date',                   // Carbon
        'facility_fee' => 'decimal:2',
        'amenities_fee' => 'decimal:2', // Add this if you use it
        'total_fee' => 'decimal:2',
        'confirmed_at' => 'datetime', // Add this
        'cancelled_at' => 'datetime', // Add this
        'paid_at' => 'datetime', // Add this
        'payment_deadline' => 'datetime', // Add this if you use it
    ];
}
