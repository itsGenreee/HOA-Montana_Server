<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',           // Only 'hourly' for now
        'start_time',
        'end_time',
        'interval_minutes',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
