<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_user_can_get_projects()
    {
        Project::create([
            'title' => 'Test Project',
            'description' => 'Desc',
            'thumbnail_path' => 'path/img.jpg',
        ]);

        $response = $this->getJson('/api/projects');

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_guest_cannot_create_project()
    {
        $response = $this->postJson('/api/projects', [
            'title' => 'Hacked Project',
        ]);

        $response->assertStatus(401); // Unauthorized
    }

    public function test_admin_can_create_project_with_image_and_skills()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $skill = Skill::create(['name' => 'Laravel', 'icon_path' => 'icon.svg']);

        // Login as Admin (Acting As)
        $response = $this->actingAs($user)->postJson('/api/projects', [
            'title' => 'New Portfolio',
            'description' => 'Awesome project',
            'thumbnail' => UploadedFile::fake()->image('thumb.jpg'),
            'tech_stack_ids' => [$skill->id],
        ]);

        $response->assertStatus(201);

        // Cek Database
        $this->assertDatabaseHas('projects', ['title' => 'New Portfolio']);

        // Cek File Terupload
        Storage::disk('public')->assertExists($response->json('data.thumbnail_path')); // Perlu penyesuaian logika path return
    }
}
