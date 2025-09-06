<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'interval_minutes',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function fees()
    {
        return $this->hasMany(FacilityFee::class);
    }
}
