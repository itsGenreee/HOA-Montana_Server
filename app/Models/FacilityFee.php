<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityFee extends Model
{
    use HasFactory;

    protected $table = 'facility_fees';

    protected $fillable = [
        'facility_id',
        'type',        // 'base', 'shift', 'block'
        'name',        // optional: day/night, block name
        'fee',
        'start_time',  // nullable
        'end_time',    // nullable
    ];

    // Define relationship to Facility
    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
}
