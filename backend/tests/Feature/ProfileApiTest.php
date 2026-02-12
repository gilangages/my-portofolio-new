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

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('filesystems.default', 'local');
    }

    public function test_admin_can_update_profile_text_only()
    {
        $user = User::factory()->create();

        // Setup data awal (Gunakan 'about_description' sesuai error log)
        Profile::create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'job_title' => 'Old Job',
            'about_description' => 'Old Desc', // <--- PERBAIKAN DISINI
            'bio' => 'Old Bio',
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/profile', [
                'name' => 'New Name',
                'email' => 'new@example.com',
                'job_title' => 'New Job',
                'about_description' => 'New Description', // <--- PERBAIKAN DISINI
                'bio' => 'New Bio',
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Profile updated successfully']);

        $this->assertDatabaseHas('profiles', [
            'name' => 'New Name',
            'job_title' => 'New Job',
        ]);
    }

    public function test_admin_can_upload_photo_secondary_image_and_cv()
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $photo = UploadedFile::fake()->image('avatar.jpg');
        $secondaryData = UploadedFile::fake()->image('illustration.png');
        $cv = UploadedFile::fake()->create('resume.pdf', 100);

        $response = $this->actingAs($user)
            ->postJson('/api/profile', [
                'name' => 'Gilang',
                'email' => 'gilang@test.com',
                'job_title' => 'Fullstack',
                'about_description' => 'Coding', // <--- PERBAIKAN DISINI (Wajib diisi)
                'bio' => 'Hello',
                'photo' => $photo,
                'secondary_image' => $secondaryData,
                'cv' => $cv,
            ]);

        // Debugging: Jika masih error, uncomment ini
        // $response->dump();

        $response->assertStatus(200);

        $profile = Profile::first();

        $this->assertNotNull($profile->photo_path, 'Photo path null');
        $this->assertNotNull($profile->secondary_image, 'Secondary Image path null');
        $this->assertNotNull($profile->cv_path, 'CV path null');

        Storage::disk('local')->assertExists($profile->photo_path);
        Storage::disk('local')->assertExists($profile->secondary_image);
        Storage::disk('local')->assertExists($profile->cv_path);
    }

    public function test_validation_error_works()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/profile', [
                'name' => '',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}
