<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'address'    => 'Metro Montaña Village, Brgy. Burgos, Rodriguez, Rizal',
            'email'      => 'johndoe@gmail.com',
            'password'   => Hash::make('password123'),
            'status'     => User::STATUS_UNVERIFIED,
        ]);

        User::create([
            'first_name' => 'Francis Edgard',
            'last_name'  => 'Ibanez',
            'address'    => 'Phs 18 B2 Block 6 Lot 9 Pilot Area 3, Brgy. Commonwealth, Quezon City',
            'email'      => 'francisedgard.ibanez@gmail.com',
            'password'   => Hash::make('password123'),
            'status'     => User::STATUS_UNVERIFIED,
        ]);

        User::create([
            'first_name' => 'Jhon Amante',
            'last_name'  => 'Sales',
            'address'    => 'B-8, L-15, Pinatubo Street, Montaña Village 1, Burgos, Rodriguez, Rizal',
            'email'      => 'jhonamante.sales@gmail.com',
            'password'   => Hash::make('password123'),
            'status'     => User::STATUS_UNVERIFIED,
        ]);
    }
}
