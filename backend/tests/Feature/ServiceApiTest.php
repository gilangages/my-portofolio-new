<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceApiTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_admin_can_create_service()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/services', [
            'title' => 'SEO Optimization',
            'description' => 'Meningkatkan rank Google.',
            'icon' => 'mdi:google',
            'is_active' => true,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('services', ['title' => 'SEO Optimization']);
    }

    public function test_guest_cannot_create_service()
    {
        $response = $this->postJson('/api/services', ['title' => 'Hack']);
        $response->assertStatus(401);
    }
}
