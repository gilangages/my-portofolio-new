<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_single_contact()
    {
        // PERBAIKAN: Ganti 'name' menjadi 'platform_name' sesuai migration
        $contact = Contact::create([
            'platform_name' => 'LinkedIn', // Sebelumnya salah ('name')
            'url' => 'https://linkedin.com/in/me',
            'icon_path' => 'path/to/icon.png',
        ]);

        $response = $this->getJson('/api/contacts/' . $contact->id);

        $response->assertStatus(200)
            ->assertJson([
                'platform_name' => 'LinkedIn',
                'url' => 'https://linkedin.com/in/me',
            ]);
    }

    public function test_get_single_contact_not_found()
    {
        $this->getJson('/api/contacts/999')->assertStatus(404);
    }

    public function test_public_user_can_get_contacts()
    {
        Contact::create(['platform_name' => 'IG', 'url' => 'http://ig.com', 'icon_path' => 'ig.png']);
        $response = $this->getJson('/api/contacts');
        $response->assertStatus(200)->assertJsonCount(1);
    }

    public function test_admin_can_create_contact()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/contacts', [
            'platform_name' => 'LinkedIn',
            'url' => 'https://linkedin.com/me',
            'icon' => UploadedFile::fake()->image('li.png'),
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('contacts', ['platform_name' => 'LinkedIn']);
    }

    public function test_admin_can_delete_contact()
    {
        $user = User::factory()->create();
        $contact = Contact::create(['platform_name' => 'Del', 'url' => 'http://del.com', 'icon_path' => 'del.png']);

        $response = $this->actingAs($user)->deleteJson("/api/contacts/{$contact->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
