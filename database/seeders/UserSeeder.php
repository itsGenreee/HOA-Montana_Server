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
            'email'      => 'johndoe@gmail.com',
            'password'   => Hash::make('password123'),
            'status'     => User::STATUS_UNVERIFIED,
        ]);

        User::create([
            'first_name' => 'Francis Edgard',
            'last_name'  => 'Ibanez',
            'email'      => 'francisedgard.ibanez@gmail.com',
            'password'   => Hash::make('password123'),
            'status'     => User::STATUS_UNVERIFIED,
        ]);

        User::create([
            'first_name' => 'John Cedric',
            'last_name'  => 'Lorenzo',
            'email'      => 'johncedric.lorenzo@gmail.com',
            'password'   => Hash::make('password123'),
            'status'     => User::STATUS_UNVERIFIED,
        ]);

        User::create([
            'first_name' => 'Jhanra',
            'last_name'  => 'Ordoviz',
            'email'      => 'jhanra.ordoviz@gmail.com',
            'password'   => Hash::make('password123'),
            'status'     => User::STATUS_UNVERIFIED,
        ]);
    }
}
