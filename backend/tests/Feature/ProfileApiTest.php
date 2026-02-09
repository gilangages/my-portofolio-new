<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_get_profile()
    {
        Storage::fake('public');
        Config::set('filesystems.default', 'public');

        Profile::create([
            'name' => 'Abdian',
            'job_title' => 'Dev',
            'about_description' => 'Hello',
            'photo_path' => 'profile/me.jpg',
        ]);

        $response = $this->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJsonPath('about.name', 'Abdian')
            ->assertJsonPath('about.photo_url', '/storage/profile/me.jpg');
    }

    public function test_admin_can_update_profile_local()
    {
        Storage::fake('public');
        Config::set('filesystems.default', 'public');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/profile', [
            'name' => 'Local Update',
            'job_title' => 'Web Dev',
            'about_description' => 'Local Desc',
            'photo' => UploadedFile::fake()->image('local.jpg'),
        ]);

        $response->assertStatus(200);

        $path = Profile::first()->photo_path;
        Storage::disk('public')->assertExists($path);
    }

    public function test_admin_can_update_profile_in_production_environment()
    {
        // 1. Setup Environment Production (Cloudinary)
        Config::set('filesystems.default', 'cloudinary');
        Storage::fake('cloudinary');

        $user = User::factory()->create();

        // 2. Action Update
        $response = $this->actingAs($user)->postJson('/api/profile', [
            'name' => 'Production Update',
            'job_title' => 'Cloud Engineer',
            'about_description' => 'Cloud Desc',
            'photo' => UploadedFile::fake()->image('prod-image.jpg'),
        ]);

        $response->assertStatus(200);

        // 3. Verifikasi Database (Data Teks)
        $profile = Profile::first();
        $this->assertEquals('Production Update', $profile->name);

        // 4. Verifikasi File (Storage)
        // Pastikan file tersimpan di disk 'cloudinary'
        Storage::disk('cloudinary')->assertExists($profile->photo_path);

        // 5. Verifikasi URL (JSON Response)
        // PERBAIKAN: Kita cek URL dari response JSON, bukan dari model $profile
        // karena $profile->photo_url tidak tersimpan di database.
        $response->assertJsonPath('data.photo_url', fn($url) => !is_null($url));
    }
}
