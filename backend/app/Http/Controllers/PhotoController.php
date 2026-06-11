<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\UpdatePhotoRequest;
use App\Models\Photo;
use Cloudinary\Configuration\Configuration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
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

    public function index()
    {
        $photos = Photo::latest()->get();
        
        foreach ($photos as $photo) {
            $photo->image_url = $this->resolveUrl($photo->image_url);
        }

        return response()->json($photos);
    }

    public function show($id)
    {
        $photo = Photo::findOrFail($id);
        $photo->image_url = $this->resolveUrl($photo->image_url);
        
        return response()->json($photo);
    }

    public function store(StorePhotoRequest $request)
    {
        $data = $request->validated();
        
        $disk = config('filesystems.default', 'local');
        $uploadApi = null;

        if ($disk === 'cloudinary') {
            $this->initCloudinary();
            $uploadApi = new \Cloudinary\Api\Upload\UploadApi();
        }

        $files = [];
        if ($request->hasFile('images')) {
            $files = $request->file('images');
        } elseif ($request->hasFile('image')) {
            $files = [$request->file('image')];
        }

        $createdPhotos = [];

        foreach ($files as $file) {
            $imageUrl = $this->handleFileUpload(
                $file,
                'photos',
                null,
                $disk,
                $uploadApi
            );

            $createdPhotos[] = Photo::create([
                'image_url' => $imageUrl
            ]);
        }

        return response()->json([
            'message' => count($createdPhotos) > 1 ? 'Photos created successfully' : 'Photo created successfully',
            'data' => count($createdPhotos) > 1 ? $createdPhotos : $createdPhotos[0]
        ], 201);
    }

    public function update(UpdatePhotoRequest $request, $id)
    {
        $photo = Photo::findOrFail($id);
        $data = $request->validated();

        $disk = config('filesystems.default', 'local');
        $uploadApi = null;

        if ($disk === 'cloudinary') {
            $this->initCloudinary();
            $uploadApi = new \Cloudinary\Api\Upload\UploadApi();
        }

        if ($request->hasFile('image')) {
            $data['image_url'] = $this->handleFileUpload(
                $request->file('image'),
                'photos',
                $photo->image_url,
                $disk,
                $uploadApi
            );
        }

        $photo->update($data);

        return response()->json([
            'message' => 'Photo updated successfully',
            'data' => $photo
        ]);
    }

    public function destroy($id)
    {
        $photo = Photo::findOrFail($id);
        
        $disk = config('filesystems.default', 'local');
        
        // Delete image
        if ($photo->image_url) {
            if ($disk === 'cloudinary' && !str_starts_with($photo->image_url, 'http')) {
                 // Cloudinary handles cleanup or we leave it. Or implement destroy via Cloudinary api.
                 // We will skip cloudinary delete for simplicity just like in profile
            } else if (!str_starts_with($photo->image_url, 'http')) {
                 if (Storage::disk($disk)->exists($photo->image_url)) {
                     Storage::disk($disk)->delete($photo->image_url);
                 }
            }
        }

        $photo->delete();

        return response()->json([
            'message' => 'Photo deleted successfully'
        ]);
    }

    private function handleFileUpload($file, $folder, $oldPath, $disk, $uploadApi)
    {
        try {
            if ($disk === 'cloudinary') {
                $result = $uploadApi->upload($file->getRealPath(), [
                    'folder' => $folder,
                    'resource_type' => 'image',
                    'access_mode' => 'public',
                    'overwrite' => true,
                ]);
                
                // Inject f_auto,q_auto into the Cloudinary URL to serve WebP/AVIF automatically
                $optimizedUrl = str_replace('/upload/', '/upload/f_auto,q_auto/', $result['secure_url']);
                return $optimizedUrl;
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
