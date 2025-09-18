<?php

namespace App\Models;

use App\Models\ReservationAmenity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Amenity extends Model
{
    use HasFactory;

    // Table name (optional if it matches "amenities")
    protected $table = 'amenities';

    // Mass assignable fields
    protected $fillable = [
        'name',
        'price',
        'max_quantity',
    ];

    // Relationships
    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_amenities')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }
}
