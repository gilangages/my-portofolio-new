<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeaturedProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_filter_featured_projects()
    {
        // 1. Setup User (Jika perlu login, uncomment line di bawah)
        // $user = User::factory()->create();
        // $this->actingAs($user);

        // 2. Buat Project yang Featured
        Project::create([
            'title' => 'Featured Project',
            'slug' => 'featured-project', // Slug biasanya unique/required
            'description' => 'Desc',
            'tech_stack' => ['Vue', 'Laravel'], // Mengisi array karena dicasting json/array
            'thumbnail_path' => 'dummy/image.jpg', // <--- INI WAJIB DIISI agar tidak error NOT NULL
            'is_featured' => true, // Yang ini featured
        ]);

        // 3. Buat Project yang TIDAK Featured
        Project::create([
            'title' => 'Normal Project 1',
            'slug' => 'normal-project-1',
            'description' => 'Desc',
            'tech_stack' => ['React'],
            'thumbnail_path' => 'dummy/image2.jpg', // <--- INI JUGA DIISI
            'is_featured' => false,
        ]);

        // 4. Request ke API
        $response = $this->getJson('/api/projects?featured=1');

        // 5. Assertions
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data') // Pastikan hanya 1 data yang muncul
            ->assertJsonFragment(['title' => 'Featured Project']); // Pastikan judulnya benar
    }
}
