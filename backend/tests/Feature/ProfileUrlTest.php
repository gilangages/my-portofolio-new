<?php

namespace Tests\Feature;

use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileUrlTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_returns_correct_url_based_on_disk_configuration()
    {
        // 1. Simulasi Disk Local
        config(['filesystems.default' => 'public']);
        config(['app.url' => 'https://api-testing.com']);

        $profile = Profile::create([
            'id' => 1,
            'photo_path' => 'profile/test.jpg',
        ]);

        $response = $this->getJson('/api/profile');

        $response->assertStatus(200);
        // Pastikan URL mengandung APP_URL dan bukan localhost
        $this->assertEquals('https://api-testing.com/storage/profile/test.jpg', $response->json('about.photo_url'));

        // 2. Simulasi Disk Cloudinary (Simpan URL penuh)
        $cloudinaryUrl = 'https://res.cloudinary.com/demo/image/upload/v1/profile/test.jpg';
        $profile->update(['photo_path' => $cloudinaryUrl]);

        $response = $this->getJson('/api/profile');

        // Jika path sudah URL penuh, harus tetap URL tersebut
        $this->assertEquals($cloudinaryUrl, $response->json('about.photo_url'));
    }
}
