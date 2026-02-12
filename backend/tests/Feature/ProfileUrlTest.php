<?php

namespace Tests\Feature;

use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ProfileUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_correct_url_based_on_disk_configuration()
    {
        Config::set('app.url', 'https://api-testing.com');
        Config::set('filesystems.default', 'public');
        Config::set('filesystems.disks.public', [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => 'https://api-testing.com/storage',
            'visibility' => 'public',
        ]);

        // PERBAIKAN: Isi field wajib agar tidak error 'Integrity constraint violation'
        $profile = Profile::create([
            'id' => 1,
            'name' => 'Test User', // <--- WAJIB
            'email' => 'test@user.com', // <--- WAJIB
            'job_title' => 'Tester', // <--- WAJIB
            'about_description' => 'Test', // <--- WAJIB
            'bio' => 'Bio', // <--- WAJIB (jika di DB not null)
            'photo_path' => 'profile/test.jpg',
            'secondary_image' => 'profile/illustration.jpg',
        ]);

        $response = $this->getJson('/api/profile');

        $response->assertStatus(200);

        $this->assertEquals(
            'https://api-testing.com/storage/profile/test.jpg',
            $response->json('about.photo_url')
        );

        // Sesuaikan dengan key JSON di Controller (secondary_image_url)
        $this->assertEquals(
            'https://api-testing.com/storage/profile/illustration.jpg',
            $response->json('about.secondary_image_url')
        );

        // Simulasi Cloudinary
        $cloudinaryUrl = 'https://res.cloudinary.com/demo/image/upload/v1/profile/test.jpg';

        $profile->update(['photo_path' => $cloudinaryUrl]);
        $profile->refresh();

        $response = $this->getJson('/api/profile');

        $this->assertEquals($cloudinaryUrl, $response->json('about.photo_url'));
    }
}
