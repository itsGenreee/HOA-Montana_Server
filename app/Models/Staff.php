<?php
// app/Models/Staff.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Staff extends Authenticatable
{
    use HasFactory, HasApiTokens;

        public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = 'staffs';
    }

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function checkedInReservations()
    {
        return $this->hasMany(Reservation::class, 'checked_in_by');
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)->firstOrFail();
    }
}
