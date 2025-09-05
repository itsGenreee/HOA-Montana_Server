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
    }
}
