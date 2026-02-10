<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Contact;
use App\Models\Profile;
use Cloudinary\Configuration\Configuration;
use Illuminate\Support\Facades\Log;
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
        // 1. Ambil data text yang sudah divalidasi
        // exclude photo & cv karena dihandle manual di bawah
        $data = $request->safe()->except(['photo', 'cv']);

        $profile = Profile::firstOrNew(['id' => 1]);
        $disk = env('FILESYSTEM_DISK', 'local');

        // Setup Cloudinary Instance jika diperlukan
        $uploadApi = null;
        if ($disk === 'cloudinary') {
            $this->initCloudinary();
            $uploadApi = new \Cloudinary\Api\Upload\UploadApi();
        }

        // 2. Handle Photo
        if ($request->hasFile('photo')) {
            try {
                if ($disk === 'cloudinary') {
                    $result = $uploadApi->upload($request->file('photo')->getRealPath(), [
                        'folder' => 'profile',
                        'resource_type' => 'image',
                        // SAYA HAPUS 'public_id' agar nama file acak & URL berubah (mencegah browser cache)
                        'overwrite' => true,
                    ]);
                    $data['photo_path'] = $result['secure_url'];
                } else {
                    // Local: Hapus file lama
                    if ($profile->photo_path && !str_starts_with($profile->photo_path, 'http')) {
                        Storage::disk($disk)->delete($profile->photo_path);
                    }
                    $data['photo_path'] = $request->file('photo')->store('profile', $disk);
                }
            } catch (\Exception $e) {
                Log::error("Photo Upload Error: " . $e->getMessage());
                return response()->json(['message' => 'Upload Photo Gagal'], 500);
            }
        }

        // 3. Handle CV
        if ($request->hasFile('cv')) {
            try {
                if ($disk === 'cloudinary') {
                    $result = $uploadApi->upload($request->file('cv')->getRealPath(), [
                        'folder' => 'cv',
                        'resource_type' => 'auto',
                        'access_mode' => 'public',
                        'type' => 'upload', // Penting agar bisa didownload public
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

        // 4. Simpan ke Database
        $profile->fill($data); // Pastikan $fillable ada di Model!
        $profile->save();

        $profile->refresh();

        // Helper untuk response URL
        $profile->photo_url = $this->resolveUrl($profile->photo_path);
        $profile->cv_url = $this->resolveUrl($profile->cv_path);

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $profile,
        ]);
    }

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

    // Helper Function agar DRY (Don't Repeat Yourself)
    private function resolveUrl($path)
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }
        // Cloudinary URL
        return Storage::url($path); // Local URL
    }
}
