<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'facility',
        'date',
        'start_time',
        'end_time',
        'fee',
        'status',
    ];

    /**
     * Define the relationship to the user who made the reservation
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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
