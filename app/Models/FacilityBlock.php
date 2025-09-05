<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'block_name',
        'start_time',
        'end_time',
        'fee',
    ];

    /**
     * A block belongs to a facility.
     */
    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
}
