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
        'facility',
        'date',
        'start_time',
        'end_time',
        'fee',
        'status',
        'reservation_token',
        'digital_signature',
        'payment_id',
    ];

    protected static function booted()
    {
        static::creating(function ($reservation) {
            $reservation->reservation_token = Str::uuid(); // Generate unique token
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

    /**
     * Optional: Cast date and time fields
     */
    protected $casts = [
        'date' => 'date',             // will cast to Carbon instance
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'fee' => 'decimal:2',
    ];
}
