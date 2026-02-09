<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Profile;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
// Import Library Cloudinary Manual
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    // Fungsi untuk inisialisasi koneksi Cloudinary secara manual
    // Mengambil kredensial langsung dari ENV Render
    private function initCloudinary()
    {
        // Cek apakah ENV terbaca (untuk debugging)
        if (!env('CLOUDINARY_URL')) {
            Log::error('CLOUDINARY_URL tidak ditemukan di ENV!');
        }

        // Setup Config Manual (Bypass file config/cloudinary.php yang error)
        Configuration::instance([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET')],
            'url' => [
                'secure' => true]]);
    }

    public function update(Request $request)
    {
        // 1. Setup Cloudinary
        $this->initCloudinary();

        $profile = Profile::firstOrNew(['id' => 1]);
        $data = $request->except(['photo', 'cv']);

        // 2. Handle Upload Photo (Manual Way)
        if ($request->hasFile('photo')) {
            try {
                $file = $request->file('photo');
                $upload = new UploadApi();

                // Upload ke folder 'profile' di Cloudinary
                $result = $upload->upload($file->getRealPath(), [
                    'folder' => 'profile',
                    'resource_type' => 'image',
                ]);

                // Simpan URL HTTPS langsung ke database
                $data['photo_path'] = $result['secure_url'];

            } catch (\Exception $e) {
                return response()->json(['message' => 'Upload Photo Gagal', 'error' => $e->getMessage()], 500);
            }
        }

        // 3. Handle Upload CV (Manual Way)
        if ($request->hasFile('cv')) {
            try {
                $file = $request->file('cv');
                $upload = new UploadApi();

                // Upload sebagai 'raw' atau 'auto' agar PDF bisa masuk
                $result = $upload->upload($file->getRealPath(), [
                    'folder' => 'cv',
                    'resource_type' => 'auto',
                ]);

                $data['cv_path'] = $result['secure_url'];

            } catch (\Exception $e) {
                return response()->json(['message' => 'Upload CV Gagal', 'error' => $e->getMessage()], 500);
            }
        }

        $profile->fill($data)->save();

        // Kembalikan URL yang sudah tersimpan di database
        $profile->photo_url = $profile->photo_path;
        $profile->cv_url = $profile->cv_path;

        return response()->json(['message' => 'Profile updated successfully', 'data' => $profile]);
    }

    public function index()
    {
        $profile = Profile::first();
        $contacts = Contact::all();

        if ($profile) {
            // Karena kita simpan URL full (https://...), langsung pakai saja
            $profile->photo_url = $profile->photo_path;
            $profile->cv_url = $profile->cv_path;
        }

        return response()->json([
            'about' => $profile,
            'social_media' => $contacts,
        ]);
    }
}
