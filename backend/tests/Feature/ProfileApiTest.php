<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config; // Penting!
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // 1. Force config agar Controller dan Test sepakat pakai disk 'local'
        Config::set('filesystems.default', 'local');
    }

    public function test_admin_can_update_profile_text_only()
    {
        $user = User::factory()->create();
        // Buat profile awal agar update tidak membuat baru (opsional, tergantung logic)
        Profile::create([
            'name' => 'Old Name',
            'job_title' => 'Old Job',
            'about_description' => 'Old Desc',
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/profile', [
                'name' => 'New Name',
                'job_title' => 'New Job',
                'about_description' => 'New Description',
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Profile updated successfully']);

        $this->assertDatabaseHas('profiles', [
            'name' => 'New Name',
            'job_title' => 'New Job',
        ]);
    }

    public function test_admin_can_upload_photo_and_cv()
    {
        // 2. Fake storage 'local' (sesuai config di atas)
        Storage::fake('local');

        $user = User::factory()->create();

        $photo = UploadedFile::fake()->image('avatar.jpg');
        $cv = UploadedFile::fake()->create('resume.pdf', 100);

        $response = $this->actingAs($user)
            ->postJson('/api/profile', [
                'name' => 'Gilang',
                'job_title' => 'Dev',
                'about_description' => 'Desc',
                'photo' => $photo,
                'cv' => $cv,
            ]);

        // Debugging: Jika error 500, uncomment baris ini untuk lihat error aslinya
        // $response->dump();

        $response->assertStatus(200);

        $profile = Profile::first();

        // 3. Pastikan path tidak null sebelum assertExists
        $this->assertNotNull($profile->photo_path, 'Photo path tidak tersimpan di DB');
        $this->assertNotNull($profile->cv_path, 'CV path tidak tersimpan di DB');

        // 4. Assert file ada di disk 'local'
        Storage::disk('local')->assertExists($profile->photo_path);
        Storage::disk('local')->assertExists($profile->cv_path);
    }

    public function test_validation_error_works()
    {
        $user = User::factory()->create();

        // Kirim data kosong. Karena 'name' dll required, harusnya return 422.
        $response = $this->actingAs($user)
            ->postJson('/api/profile', [
                'name' => '',
                'job_title' => '',
                // about_description sengaja dihilangkan agar error
            ]);

        // Jika masih 500, uncomment ini untuk melihat error stack trace
        // $response->dump();

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'job_title', 'about_description']);
    }
}
