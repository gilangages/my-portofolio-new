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

    public function test_admin_can_store_certificate_with_image()
    {
        Storage::fake('public'); // Simulasikan storage agar tidak mengotori folder asli
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/certificates', [
            'title' => 'Laravel Advanced',
            'issuer' => 'Laracasts',
            'description' => 'Certificate of completion',
            'image' => UploadedFile::fake()->image('cert.jpg'),
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Laravel Advanced');

        // Pastikan image_url ada di respons JSON
        $this->assertNotNull($response->json('data.image_url'));

        // Pastikan file benar-benar tersimpan di disk
        $path = Certificate::first()->image_path;
        Storage::disk('public')->assertExists($path);
    }

    public function test_can_get_certificates_with_automatic_url()
    {
        Certificate::create([
            'title' => 'Test Cert',
            'issuer' => 'Test Issuer',
            'description' => 'Test Desc',
            'image_path' => 'certificates/sample.jpg',
        ]);

        $response = $this->getJson('/api/certificates');

        $response->assertStatus(200);
        // Memastikan item pertama memiliki key image_url hasil dari accessor
        $this->assertArrayHasKey('image_url', $response->json()[0]);
    }

    public function test_file_is_deleted_when_certificate_is_destroyed()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('cert_to_delete.jpg');
        $path = $file->store('certificates');

        $cert = Certificate::create([
            'title' => 'Delete Me',
            'issuer' => 'Issuer',
            'description' => 'Desc',
            'image_path' => $path,
        ]);

        $this->actingAs($user)->deleteJson("/api/certificates/{$cert->id}");

        // Pastikan data di database hilang
        $this->assertDatabaseMissing('certificates', ['id' => $cert->id]);
        // Pastikan file fisik juga terhapus dari storage
        Storage::disk('public')->assertMissing($path);
    }
    public function test_api_returns_full_image_url()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/certificates', [
            'title' => 'Test Cert',
            'issuer' => 'Test Issuer',
            'description' => 'Test Desc',
            'image' => UploadedFile::fake()->image('test.jpg'),
        ]);

        $response->assertStatus(201);

        // Memastikan field image_url tersedia di response
        $this->assertNotNull($response->json('data.image_url'));

        // Memastikan URL mengandung path yang benar (bisa lokal /app/public atau cloud)
        $this->assertStringContainsString('certificates/', $response->json('data.image_url'));
    }

    public function test_deleting_certificate_removes_file()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('cert.jpg');
        $path = $file->store('certificates');

        $cert = Certificate::create([
            'title' => 'Del', 'issuer' => 'Del', 'description' => 'Del', 'image_path' => $path,
        ]);

        $this->actingAs($user)->deleteJson("/api/certificates/{$cert->id}");

        Storage::disk('public')->assertMissing($path);
    }
}
