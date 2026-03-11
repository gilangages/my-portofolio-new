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
    }

    public function test_returning_visitor_updates_device_details()
    {
        // Simulate a visitor first logged without device data (or old data)
        Visitor::create([
            'device_id' => 'returning-device-456',
            'device_type' => 'unknown',
            'os' => null,
            'browser' => null,
            'device_name' => null,
        ]);

        // Same device returns, now we parse User-Agent
        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        ])->postJson('/api/visitors', [
            'device_id' => 'returning-device-456',
        ]);

        $response->assertStatus(201);

        // Ensure database count hasn't increased, but row updated
        $this->assertDatabaseCount('visitors', 1);
        $this->assertDatabaseHas('visitors', [
            'device_id' => 'returning-device-456',
            'device_type' => 'desktop',
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
