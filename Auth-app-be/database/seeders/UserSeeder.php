<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin korisnik
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now()
        ]);

        // Obični korisnik
        User::create([
            'name' => 'Test Korisnik',
            'email' => 'user@test.com',
            'password' => Hash::make('user123'),
            'email_verified_at' => now()
        ]);
    }
}
