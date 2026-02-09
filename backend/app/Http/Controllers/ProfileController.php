<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Profile;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;

// Import Library Cloudinary Native (Manual SDK)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Inisialisasi Konfigurasi Cloudinary Secara Manual.
     * Fungsi ini memastikan kita tidak bergantung pada file config/cloudinary.php
     */
    private function initCloudinary()
    {
        // Konfigurasi manual mengambil langsung dari ENV
        Configuration::instance([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true, // WAJIB: Memastikan return URL selalu HTTPS
            ],
        ]);
    }

    public function update(Request $request)
    {
        // 1. Panggil konfigurasi Cloudinary
        $this->initCloudinary();

        // 2. Ambil atau buat profile (ID 1)
        $profile = Profile::firstOrNew(['id' => 1]);

        // Ambil semua data request kecuali file
        $data = $request->except(['photo', 'cv']);

        // 3. Handle Upload Photo
        if ($request->hasFile('photo')) {
            try {
                $file = $request->file('photo');

                // Instance UploadApi dari library native
                $upload = new UploadApi();

                // Upload ke Cloudinary folder 'profile'
                $result = $upload->upload($file->getRealPath(), [
                    'folder' => 'profile',
                    'resource_type' => 'image',
                    'overwrite' => true, // Timpa jika nama file sama (opsional)
                ]);

                // PENTING: Ambil 'secure_url' agar dapat HTTPS
                $data['photo_path'] = $result['secure_url'];

            } catch (\Exception $e) {
                Log::error("Cloudinary Photo Upload Error: " . $e->getMessage());
                return response()->json([
                    'message' => 'Upload Photo Gagal',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

        // 4. Handle Upload CV
        if ($request->hasFile('cv')) {
            try {
                $file = $request->file('cv');
                $upload = new UploadApi();

                // Upload CV (gunakan resource_type 'auto' agar support PDF)
                $result = $upload->upload($file->getRealPath(), [
                    'folder' => 'cv',
                    'resource_type' => 'auto',
                ]);

                // Simpan URL HTTPS
                $data['cv_path'] = $result['secure_url'];

            } catch (\Exception $e) {
                Log::error("Cloudinary CV Upload Error: " . $e->getMessage());
                return response()->json([
                    'message' => 'Upload CV Gagal',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

        // 5. Simpan ke Database
        $profile->fill($data);
        $profile->save();

        // Refresh model untuk memastikan data terbaru
        $profile->refresh();

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $profile,
        ]);
    }

    public function index()
    {
        $profile = Profile::first();
        $contacts = Contact::all();

        // Format data agar frontend mudah membacanya
        if ($profile) {
            // Mapping path database ke key yang diharapkan frontend
            // Jika path berisi 'http' (link external/cloudinary), pakai langsung.
            // Jika tidak, biarkan apa adanya (atau handle default image).
            $profile->photo_url = $profile->photo_path;
            $profile->cv_url = $profile->cv_path;
        }

        return response()->json([
            'about' => $profile,
            'social_media' => $contacts,
        ]);
    }
}
