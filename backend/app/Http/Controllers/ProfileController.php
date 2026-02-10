<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Contact;
use App\Models\Profile;
use Cloudinary\Configuration\Configuration;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
// Pastikan Import ini ada! Jika tidak, validation request bisa bikin error 500
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    private function initCloudinary()
    {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true,
            ],
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        // 1. Validasi Data
        // Jika request tidak valid, Laravel otomatis stop di sini dan return 422.
        // Jika anda dapat error 500 di test validation, cek Log/Import.

        $data = $request->safe()->except(['photo', 'cv']);

        // 2. Ambil Profile (Logic diperbaiki)
        $profile = Profile::first();
        if (!$profile) {
            $profile = new Profile();
            // Opsional: Set ID 1 jika ingin maksa single row
            // $profile->id = 1;
        }

        // 3. Gunakan Config, jangan env() langsung. Agar bisa di-override test.
        $disk = config('filesystems.default', 'local');

        // Setup Cloudinary
        $uploadApi = null;
        if ($disk === 'cloudinary') {
            $this->initCloudinary();
            $uploadApi = new \Cloudinary\Api\Upload\UploadApi();
        }

        // 4. Handle Photo
        if ($request->hasFile('photo')) {
            try {
                if ($disk === 'cloudinary') {
                    $result = $uploadApi->upload($request->file('photo')->getRealPath(), [
                        'folder' => 'profile',
                        'resource_type' => 'image',
                        'overwrite' => true,
                    ]);
                    $data['photo_path'] = $result['secure_url'];
                } else {
                    // Hapus file lama (Local)
                    if ($profile->photo_path && !str_starts_with($profile->photo_path, 'http')) {
                        Storage::disk($disk)->delete($profile->photo_path);
                    }
                    // Simpan file baru
                    // PERBAIKAN: Gunakan $disk eksplisit saat store
                    $data['photo_path'] = $request->file('photo')->store('profile', $disk);
                }
            } catch (\Exception $e) {
                Log::error("Photo Upload Error: " . $e->getMessage());
                return response()->json(['message' => 'Upload Photo Gagal'], 500);
            }
        }

        // 5. Handle CV
        if ($request->hasFile('cv')) {
            try {
                if ($disk === 'cloudinary') {
                    $result = $uploadApi->upload($request->file('cv')->getRealPath(), [
                        'folder' => 'cv',
                        'resource_type' => 'auto',
                        'access_mode' => 'public',
                        'type' => 'upload',
                    ]);
                    $data['cv_path'] = $result['secure_url'];
                } else {
                    if ($profile->cv_path && !str_starts_with($profile->cv_path, 'http')) {
                        Storage::disk($disk)->delete($profile->cv_path);
                    }
                    $data['cv_path'] = $request->file('cv')->store('cv', $disk);
                }
            } catch (\Exception $e) {
                Log::error("CV Upload Error: " . $e->getMessage());
                return response()->json(['message' => 'Upload CV Gagal'], 500);
            }
        }

        $profile->fill($data);
        $profile->save();
        $profile->refresh();

        $profile->photo_url = $this->resolveUrl($profile->photo_path);
        $profile->cv_url = $this->resolveUrl($profile->cv_path);

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $profile,
        ]);
    }

    // ... method index dan resolveUrl tetap sama
    public function index()
    {
        $profile = Profile::first();
        $contacts = Contact::all();

        if ($profile) {
            $profile->photo_url = $this->resolveUrl($profile->photo_path);
            $profile->cv_url = $this->resolveUrl($profile->cv_path);
        }

        return response()->json([
            'about' => $profile,
            'social_media' => $contacts,
        ]);
    }

    private function resolveUrl($path)
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return Storage::url($path);
    }
}
