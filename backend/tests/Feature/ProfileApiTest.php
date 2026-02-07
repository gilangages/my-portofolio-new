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

    public function test_public_can_get_profile()
    {
        // Karena Profile singleton, pastikan datanya ada
        Profile::create([
            'name' => 'Abdian',
            'job_title' => 'Dev',
            'about_description' => 'Hello',
            'photo_path' => 'me.jpg',
        ]);

        $response = $this->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJsonPath('about.name', 'Abdian');
    }

    public function test_admin_can_update_profile()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        // Buat data awal (atau biarkan controller create if not exists)
        Profile::create([
            'name' => 'Old',
            'job_title' => 'Old Job',
            'about_description' => 'Desc',
        ]);

        $response = $this->actingAs($user)->postJson('/api/profile', [
            'name' => 'Abdian Updated',
            'job_title' => 'Fullstack',
            'about_description' => 'New Desc',
            'photo' => UploadedFile::fake()->image('new-me.jpg'),
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('profiles', ['name' => 'Abdian Updated']);
    }
}
