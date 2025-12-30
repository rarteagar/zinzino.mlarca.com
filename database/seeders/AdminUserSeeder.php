<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $user = User::create([
            'name' => 'Administrator General',
            'email' => 'contacto@robertoarteaga.net',
            'password' => Hash::make('secret'),
            'whatsapp' => '51995750181',
            'email_verified_at' => now(),
        ]);

        // Asignar rol de administrador
        if (! $user->hasRole('super-admin')) {
            $user->assignRole('super-admin');
        }
    }
}
