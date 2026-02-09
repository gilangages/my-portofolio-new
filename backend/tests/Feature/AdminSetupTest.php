<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSetupTest extends TestCase
{
    // RefreshDatabase akan mereset DB setiap kali test jalan
    // Jadi aman, tidak mengotori database asli
    use RefreshDatabase;

    /**
     * Test 1: Pastikan Seeder bisa jalan tanpa error 'fake()'
     * dan data admin masuk ke database.
     */
    public function test_seeder_creates_admin_successfully()
    {
        // Jalankan perintah db:seed
        $this->seed();

        // Cek apakah email admin ada di database
        $this->assertDatabaseHas('users', [
            'email' => 'qbdian@gmail.com',
            'name' => 'Abdian',
        ]);
    }

    /**
     * Test 2: Pastikan kalau seeder dijalankan berkali-kali (deploy ulang),
     * admin tidak terduplikasi.
     */
    public function test_seeder_is_idempotent_no_duplicates()
    {
        // Jalankan seeder pertama kali
        $this->seed();

        // Jalankan seeder kedua kali (simulasi deploy berulang)
        $this->seed();

        // Pastikan jumlah user dengan email tersebut tetap CUMA 1
        $count = User::where('email', 'qbdian@gmail.com')->count();
        $this->assertEquals(1, $count, 'Admin user should not be duplicated when seeding runs twice.');
    }

    /**
     * Test 3: Pastikan password yang di-hardcode di seeder valid
     * dan bisa digunakan untuk login via API.
     */
    public function test_admin_can_login_with_seeded_credentials()
    {
        // 1. Setup data (jalankan seeder)
        $this->seed();

        // 2. Tembak API Login
        $response = $this->postJson('api/admin/login', [ // Sesuaikan URL dengan routes/api.php kamu
            'email' => 'qbdian@gmail.com',
            'password' => 'haloqbdian2121',
        ]);

        // 3. Harapannya Status 200 OK dan struktur JSON sesuai AuthController
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id',
                    'name',
                    'email',
                    // 'created_at', // Opsional, bisa ditambah jika perlu
                    // 'updated_at',
                ],
                'token',
            ]);
    }
    public function test_swagger_documentation_page_loads_correctly()
    {
        // Akses route /api-docs (sesuaikan jika URL kamu beda, misal /docs)
        $response = $this->get('/api-docs');

        // Pastikan status OK (200)
        $response->assertStatus(200);

        // Pastikan ada teks "Swagger UI" di dalamnya (tanda halaman benar)
        $response->assertSee('Swagger UI');

        // Opsional: Pastikan file JSON admin juga bisa diakses
        // (Kalau kamu pakai secure_asset, ini memastikan linknya ke-generate)
        $response->assertSee('admin-api.json');
    }
}
