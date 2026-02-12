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
        // 1. Ambil data teks
        $data = $request->safe()->except(['photo', 'cv', 'secondary_image']);

        // 2. Ambil Profile atau Siapkan Instance Baru
        // PENTING: Gunakan firstOrNew.
        // Ini TIDAK menyimpan ke DB dulu, jadi aman dari error "Field 'name' doesn't have a default value"
        $profile = Profile::firstOrNew([], ['id' => 1]);

        // 3. Config Disk
        $disk = config('filesystems.default', 'local');
        $uploadApi = null;

        if ($disk === 'cloudinary') {
            $this->initCloudinary();
            $uploadApi = new \Cloudinary\Api\Upload\UploadApi();
        }

        // 4. Handle Uploads
        // (Logika ini tetap jalan normal karena $profile->photo_path tersedia jika data ada)

        // Handle Primary Photo
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $this->handleFileUpload(
                $request->file('photo'),
                'profile',
                $profile->photo_path,
                $disk,
                $uploadApi
            );
        }

        // Handle Secondary Image
        if ($request->hasFile('secondary_image')) {
            $data['secondary_image'] = $this->handleFileUpload(
                $request->file('secondary_image'),
                'profile-secondary',
                $profile->secondary_image,
                $disk,
                $uploadApi
            );
        }

        // Handle CV
        if ($request->hasFile('cv')) {
            $data['cv_path'] = $this->handleFileUpload(
                $request->file('cv'),
                'cv',
                $profile->cv_path,
                $disk,
                $uploadApi,
                'auto'
            );
        }

        // 5. Save & Refresh
        // DISINILAH penyimpanan ke Database terjadi.
        // Karena $data sudah berisi 'name', 'job_title' (dari request) DAN file paths,
        // maka Database akan menerimanya dengan sukses.
        $profile->fill($data);
        $profile->save();
        $profile->refresh();

        // 6. Append URLs
        $profile->photo_url = $this->resolveUrl($profile->photo_path);
        $profile->secondary_image_url = $this->resolveUrl($profile->secondary_image);
        $profile->cv_url = $this->resolveUrl($profile->cv_path);

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $profile,
        ]);
    }

    // ... sisa method index, handleFileUpload, dan resolveUrl JANGAN DIUBAH (tetap pakai yang lama) ...

    public function index()
    {
        $profile = Profile::first();
        $contacts = Contact::all();

        if ($profile) {
            $profile->photo_url = $this->resolveUrl($profile->photo_path);
            $profile->secondary_image_url = $this->resolveUrl($profile->secondary_image);
            $profile->cv_url = $this->resolveUrl($profile->cv_path);
        }

        return response()->json([
            'about' => $profile,
            'social_media' => $contacts,
        ]);
    }

    private function handleFileUpload($file, $folder, $oldPath, $disk, $uploadApi, $resourceType = 'image')
    {
        try {
            if ($disk === 'cloudinary') {
                $result = $uploadApi->upload($file->getRealPath(), [
                    'folder' => $folder,
                    'resource_type' => $resourceType,
                    'access_mode' => 'public',
                    'overwrite' => true,
                ]);
                return $result['secure_url'];
            } else {
                if ($oldPath && !str_starts_with($oldPath, 'http')) {
                    if (Storage::disk($disk)->exists($oldPath)) {
                        Storage::disk($disk)->delete($oldPath);
                    }
                }
                return $file->store($folder, $disk);
            }
        } catch (\Exception $e) {
            Log::error("Upload Error ({$folder}): " . $e->getMessage());
            throw $e;
        }
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
