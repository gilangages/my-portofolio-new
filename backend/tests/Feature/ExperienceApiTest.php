<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExperienceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_user_can_get_experiences()
    {
        Experience::create([
            'company_name' => 'Google',
            'role' => 'Dev',
            'status' => 'Full-time',
            'start_date' => '2023-01-01',
            'description' => 'Working hard',
        ]);

        $response = $this->getJson('/api/experiences');
        $response->assertStatus(200)->assertJsonCount(1);
    }

    public function test_admin_can_create_experience()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/experiences', [
            'company_name' => 'Facebook',
            'role' => 'Senior Dev',
            'status' => 'Full-time',
            'start_date' => '2024-01-01',
            'description' => 'Coding React',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('experiences', ['company_name' => 'Facebook']);
    }

    public function test_admin_can_update_experience()
    {
        $user = User::factory()->create();
        $exp = Experience::create([
            'company_name' => 'Start',
            'role' => 'Dev',
            'status' => 'Intern',
            'start_date' => '2023-01-01',
            'description' => 'Desc',
        ]);

        $response = $this->actingAs($user)->putJson("/api/experiences/{$exp->id}", [
            'company_name' => 'Updated Corp',
            'role' => 'Lead Dev',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('experiences', ['company_name' => 'Updated Corp']);
    }

    public function test_admin_can_delete_experience()
    {
        $user = User::factory()->create();
        $exp = Experience::create([
            'company_name' => 'Del Corp',
            'role' => 'Dev',
            'status' => 'Intern',
            'start_date' => '2023-01-01',
            'description' => 'Desc',
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/experiences/{$exp->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('experiences', ['id' => $exp->id]);
    }
}
