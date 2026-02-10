<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Buat user untuk autentikasi jika diperlukan
        $this->user = User::factory()->create();
    }

    /**
     * @test
     */
    public function it_can_upload_project_with_thumbnail()
    {
        // Mock disk yang digunakan (sesuai FILESYSTEM_DISK)
        Storage::fake(config('filesystems.default'));

        $skill = Skill::create(['name' => 'Vue.js']);

        $response = $this->actingAs($this->user)
            ->postJson('/api/projects', [
                'title' => 'Project Baru',
                'description' => 'Deskripsi project',
                'thumbnail' => UploadedFile::fake()->image('thumbnail.jpg'),
                'tech_stack_ids' => [$skill->id],
            ]);

        $response->assertStatus(201);

        // Cek apakah data masuk ke database
        $this->assertDatabaseHas('projects', ['title' => 'Project Baru']);

        // Cek apakah file benar-benar tersimpan di disk
        $project = Project::first();
        Storage::disk(config('filesystems.default'))->assertExists($project->thumbnail_path);

        // Pastikan URL thumbnail tidak mengandung localhost jika di production
        $this->assertStringNotContainsString('localhost', $project->thumbnail_url);
    }

    /**
     * @test
     */
    public function it_can_upload_certificate_with_image()
    {
        Storage::fake(config('filesystems.default'));

        $response = $this->actingAs($this->user)
            ->postJson('/api/certificates', [
                'title' => 'Sertifikat Laravel',
                'issuer' => 'Udemy',
                'description' => 'Lulus kursus backend',
                'image' => UploadedFile::fake()->image('cert.png'),
            ]);

        $response->assertStatus(201);

        $certificate = Certificate::first();
        Storage::disk(config('filesystems.default'))->assertExists($certificate->image_path);

        // Pastikan URL image bisa diakses
        $this->assertNotNull($certificate->image_url);
    }
}
