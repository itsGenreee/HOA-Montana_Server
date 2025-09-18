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
        'facility_fee',      // replaced old fee
        'total_fee',         // includes facility + amenities
        'status',
        'event_type',        // e.g. Birthday, Wedding
        'guest_count',       // number of guests
        'reservation_token',
        'digital_signature',
        'payment_id',
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

    /**
     * Optional: Casts for date/time and money
     */
    protected $casts = [
        'date' => 'date',                   // Carbon
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'facility_fee' => 'decimal:2',
        'total_fee' => 'decimal:2',
    ];
}
