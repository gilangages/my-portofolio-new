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

        $email = config('services.admin.email');
        $password = config('services.admin.password');

        if (!$email || !$password) {
            $this->command->error('Harap isi ADMIN_EMAIL dan ADMIN_PASS di file . env!');
            return;
        }

        // Cek admin, kalau belum ada langsung buat pakai User::create (bukan factory)

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Abdian',
                'password' => Hash::make($password),
                'email_verified_at' => now(),

            ]
        );

        $this->command->info("Admin User Siap: $email");

    }
}
