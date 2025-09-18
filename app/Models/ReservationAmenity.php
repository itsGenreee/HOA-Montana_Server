<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationAmenity extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'reservation_amenities';

    // Mass assignable fields
    protected $fillable = [
        'reservation_id',
        'amenity_id',
        'quantity',
        'price',
    ];

    // Relationships
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function amenity()
    {
        return $this->belongsTo(Amenity::class);
    }
}
