<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_single_certificate()
    {
        // PERBAIKAN: Sesuaikan dengan kolom di migration (title, description, image_path, dll)
        $cert = Certificate::create([
            'title' => 'AWS Certified', // Wajib (sebelumnya 'name')
            'description' => 'Certified Cloud Practitioner', // Wajib
            'image_path' => 'certs/aws.jpg', // Wajib
            'issuer' => 'Amazon', // Nullable (sebelumnya 'issued_by')
            'credential_link' => 'https://aws.amazon.com', // Nullable (sebelumnya 'file_url')
        ]);

        $response = $this->getJson('/api/certificates/' . $cert->id);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'AWS Certified',
                'issuer' => 'Amazon',
            ]);
    }

    public function test_get_single_certificate_not_found()
    {
        $this->getJson('/api/certificates/999')->assertStatus(404);
    }

    public function test_public_user_can_get_certificates()
    {
        Certificate::create([
            'title' => 'AWS Cloud',
            'issuer' => 'AWS',
            'description' => 'Hard exam',
            'image_path' => 'cert.jpg',
        ]);

        $response = $this->getJson('/api/certificates');
        $response->assertStatus(200)->assertJsonCount(1);
    }

    public function test_admin_can_create_certificate()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/certificates', [
            'title' => 'Laravel Cert',
            'issuer' => 'Laravel',
            'description' => 'Official Cert',
            'image' => UploadedFile::fake()->image('cert.jpg'),
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('certificates', ['title' => 'Laravel Cert']);
    }

    public function test_admin_can_update_certificate()
    {
        $user = User::factory()->create();
        $cert = Certificate::create([
            'title' => 'Old Cert',
            'issuer' => 'Old Issuer',
            'description' => 'Desc',
            'image_path' => 'path.jpg',
        ]);

        $response = $this->actingAs($user)->putJson("/api/certificates/{$cert->id}", [
            'title' => 'Updated Cert',
            'issuer' => 'New Issuer', // Partial update works if controller handles it or validaton is 'sometimes'
        ]);

        // Note: Pastikan UpdateCertificateRequest validation rules menggunakan 'sometimes'
        // Jika error validasi, pastikan kirim semua field required.
        // Di sini saya asumsikan request kamu sudah benar.

        // Revisi agar aman: Kirim semua data required untuk update jika validasi strict
        $response = $this->actingAs($user)->putJson("/api/certificates/{$cert->id}", [
            'title' => 'Updated Cert',
            'issuer' => 'New Issuer',
            'description' => 'Desc',
            // image tidak dikirim (nullable on update)
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('certificates', ['title' => 'Updated Cert']);
    }

    public function test_admin_can_delete_certificate()
    {
        $user = User::factory()->create();
        $cert = Certificate::create([
            'title' => 'Del Cert',
            'issuer' => 'Del',
            'description' => 'Desc',
            'image_path' => 'path.jpg',
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/certificates/{$cert->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('certificates', ['id' => $cert->id]);
    }
}
