<?php

namespace Tests\Feature;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SkillApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_user_can_get_skills()
    {
        Skill::create(['name' => 'Laravel', 'category' => 'Backend', 'icon_path' => 'icon.svg']);

        $response = $this->getJson('/api/skills');

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_guest_cannot_create_skill()
    {
        $response = $this->postJson('/api/skills', [
            'name' => 'Vue',
            'category' => 'Frontend',
        ]);
        $response->assertStatus(401);
    }

    public function test_admin_can_create_skill()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/skills', [
            'name' => 'React',
            'category' => 'Frontend',
            'icon' => UploadedFile::fake()->image('react.png'),
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('skills', ['name' => 'React']);
    }

    public function test_admin_can_update_skill()
    {
        $user = User::factory()->create();
        $skill = Skill::create(['name' => 'Old Name', 'category' => 'Tools', 'icon_path' => 'old.png']);

        $response = $this->actingAs($user)->putJson("/api/skills/{$skill->id}", [
            'name' => 'New Name',
            'category' => 'Tools',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('skills', ['name' => 'New Name']);
    }

    public function test_admin_can_delete_skill()
    {
        $user = User::factory()->create();
        $skill = Skill::create(['name' => 'To Delete', 'category' => 'Other', 'icon_path' => 'del.png']);

        $response = $this->actingAs($user)->deleteJson("/api/skills/{$skill->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
    }
}
