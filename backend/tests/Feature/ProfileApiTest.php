<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_profile_text_only()
    {
        // 1. Setup User & Token
        $user = User::factory()->create();

        // 2. Setup Data Awal
        Profile::create([
            'name' => 'Old Name',
            'job_title' => 'Old Job',
            'about_description' => 'Old Desc',
        ]);

        // 3. Hit Endpoint
        $response = $this->actingAs($user)
            ->postJson('/api/profile', [
                'name' => 'Gilang Ages',
                'job_title' => 'Backend Master',
                'about_description' => 'New Description',
            ]);

        // 4. Assertions
        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Gilang Ages');

        $this->assertDatabaseHas('profiles', [
            'name' => 'Gilang Ages',
            'job_title' => 'Backend Master',
        ]);
    }

    public function test_admin_can_upload_photo_local()
    {
        $user = User::factory()->create();
        Storage::fake('public'); // Mock storage

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)
            ->postJson('/api/profile', [
                'name' => 'Gilang',
                'job_title' => 'Dev',
                'about_description' => 'Desc',
                'photo' => $file,
            ]);

        $response->assertStatus(200);

        // Pastikan file tersimpan di path yg benar
        // Karena controller pakai hash name, kita cek keberadaan filenya di folder profile
        $this->assertNotEmpty(Storage::disk('public')->allFiles('profile'));

        // Cek DB mencatat path
        $profile = Profile::first();
        $this->assertStringContainsString('profile/', $profile->photo_path);
    }

    public function test_validation_works()
    {
        $user = User::factory()->create();

        // Kirim data kosong
        $response = $this->actingAs($user)
            ->postJson('/api/profile', []);

        // Harusnya 422 Unprocessable Entity
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'job_title', 'about_description']);
    }
}
