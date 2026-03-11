<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Visitor;
use App\Models\User;

class VisitorApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_log_visitor_with_device_details()
    {
        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
        ])->postJson('/api/visitors', [
            'device_id' => 'device-123',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('visitors', [
            'device_id' => 'device-123',
        ]);

        $this->assertDatabaseHas('visitors', [
            'device_id' => 'device-123',
        ]);
    }

    public function test_admin_can_get_all_visitors()
    {
        $admin = User::factory()->create();
        Visitor::factory()->count(3)->create();

        $response = $this->actingAs($admin)->getJson('/api/admin/visitors');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_guest_cannot_get_visitors()
    {
        $response = $this->getJson('/api/admin/visitors');

        $response->assertStatus(401);
    }
}
