<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Cek admin, kalau belum ada langsung buat pakai User::create (bukan factory)
        if (!User::where('email', 'qbdian@gmail.com')->exists()) {

            User::create([
                'name' => 'Abdian',
                'email' => 'qbdian@gmail.com',
                'password' => Hash::make('haloqbdian2121'),
                'email_verified_at' => now(), // Opsional: biar statusnya langsung verified
            ]);

            echo "Admin user created: qbdian@gmail.com\n";
        }
    }
}
