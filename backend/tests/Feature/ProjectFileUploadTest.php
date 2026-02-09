<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config; // <--- Tambahkan ini
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectFileUploadTest extends TestCase
{
    use RefreshDatabase;

    // Helper function agar kita tidak mengulang-ulang setup
    protected function setUp(): void
    {
        parent::setUp();

        // 1. Kita paksa aplikasi menganggap Default Disk adalah 'cloudinary'
        //    Ini mensimulasikan kondisi environment Production di Render
        Config::set('filesystems.default', 'cloudinary');

        // 2. Kita palsukan disk 'cloudinary' agar tidak upload beneran ke internet
        Storage::fake('cloudinary');
    }

    public function test_project_upload_stores_file_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->image('project-thumb.jpg');

        $response = $this->postJson('/api/projects', [
            'title' => 'Test Project',
            'description' => 'Desc',
            'thumbnail' => $file,
            // Isi field lain sesuai request validation Anda, misal:
            'link' => 'http://example.com',
            'date' => '2024-01-01',
        ]);

        $response->assertStatus(201);

        $project = Project::first();

        // Assert file ada di disk cloudinary (yang sudah dipalsukan)
        $this->assertNotNull($project->thumbnail_path);
        Storage::disk('cloudinary')->assertExists($project->thumbnail_path);
    }

    public function test_updating_project_image_replaces_old_file()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1. Upload awal
        $oldFile = UploadedFile::fake()->image('old.jpg');
        $this->postJson('/api/projects', [
            'title' => 'Original',
            'description' => 'Desc',
            'thumbnail' => $oldFile,
            'link' => 'http://example.com',
            'date' => '2024-01-01',
        ]);

        $project = Project::first();
        $oldPath = $project->thumbnail_path;
        Storage::disk('cloudinary')->assertExists($oldPath);

        // 2. Update
        $newFile = UploadedFile::fake()->image('new.jpg');
        $this->putJson("/api/projects/{$project->id}", [ // Pastikan method PUT/PATCH sesuai route
            'title' => 'Updated',
            'description' => 'Desc',
            'thumbnail' => $newFile,
            'link' => 'http://example.com',
            'date' => '2024-01-01',
        ]);

        $project->refresh();
        $newPath = $project->thumbnail_path;

        // Cek file lama hilang, file baru ada
        Storage::disk('cloudinary')->assertMissing($oldPath);
        Storage::disk('cloudinary')->assertExists($newPath);
    }

    public function test_deleting_project_removes_file()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->image('delete.jpg');
        $this->postJson('/api/projects', [
            'title' => 'To Delete',
            'description' => 'Desc',
            'thumbnail' => $file,
            'link' => 'http://example.com',
            'date' => '2024-01-01',
        ]);

        $project = Project::first();
        $path = $project->thumbnail_path;

        $this->deleteJson("/api/projects/{$project->id}");

        Storage::disk('cloudinary')->assertMissing($path);
    }
}
