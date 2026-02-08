<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Mengecek apakah user admin sudah ada, jika belum buat baru
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Abdian',
                'email' => 'qbdian@gmail.com',
                // Password ini akan di-hash. Ganti 'password' dengan password yang kamu mau.
                'password' => Hash::make('haloqbdian2121'),
            ]);

            echo "Admin user created: qbdian@gmail.com / haloqbdian2121\n";
        }
    }
}
