<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Profile;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\Request;
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

    public function update(Request $request)
    {
        $profile = Profile::firstOrNew(['id' => 1]);
        $data = $request->except(['photo', 'cv']);

        // Cek disk yang digunakan di .env
        $disk = env('FILESYSTEM_DISK', 'local');

        // 1. Handle Photo
        if ($request->hasFile('photo')) {
            try {
                if ($disk === 'cloudinary') {
                    // JIKA CLOUDINARY
                    $this->initCloudinary();
                    $upload = new \Cloudinary\Api\Upload\UploadApi();
                    $result = $upload->upload($request->file('photo')->getRealPath(), [
                        'folder' => 'profile',
                        'resource_type' => 'image',
                        'overwrite' => true,
                    ]);
                    $data['photo_path'] = $result['secure_url'];
                } else {
                    // JIKA LOCAL (Hapus foto lama jika ada di local)
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

        // 2. Handle CV (Logika yang sama)
        if ($request->hasFile('cv')) {
            try {
                if ($disk === 'cloudinary') {
                    $this->initCloudinary();
                    $upload = new \Cloudinary\Api\Upload\UploadApi();
                    $result = $upload->upload($request->file('cv')->getRealPath(), [
                        'folder' => 'cv',
                        'resource_type' => 'auto',
                        'access_mode' => 'public', // Tambahkan ini agar file bisa diakses publik
                        'type' => 'upload', // Pastikan tipenya adalah upload (publik)
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

        return response()->json(['message' => 'Profile updated successfully', 'data' => $profile]);
    }

    public function index()
    {
        $profile = Profile::first();
        $contacts = Contact::all();

        if ($profile) {
            // Gunakan Storage::url agar jika path-nya 'profile/file.jpg',
            // ia akan berubah jadi 'https://api.anda/storage/profile/file.jpg'
            // Jika path sudah 'https://cloudinary...', Storage::url akan mendeteksinya.
            $profile->photo_url = $profile->photo_path ? Storage::url($profile->photo_path) : null;
            $profile->cv_url = $profile->cv_path ? Storage::url($profile->cv_path) : null;
        }

        return response()->json([
            'about' => $profile,
            'social_media' => $contacts,
        ]);
    }
}
