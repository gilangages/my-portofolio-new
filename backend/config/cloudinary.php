<?php

/*
|--------------------------------------------------------------------------
| Cloudinary Configuration
|--------------------------------------------------------------------------
|
| API Cloudinary membutuhkan array key bernama 'cloud'.
| Tanpa key ini, library akan error "Undefined array key cloud".
|
 */

// Kita ambil variable dari env
$apiKey = env('CLOUDINARY_API_KEY');
$apiSecret = env('CLOUDINARY_API_SECRET');
$cloudName = env('CLOUDINARY_CLOUD_NAME');

// Prioritaskan CLOUDINARY_URL jika ada
$generatedUrl = env('CLOUDINARY_URL');
if (!$generatedUrl && $apiKey && $apiSecret && $cloudName) {
    $generatedUrl = "cloudinary://{$apiKey}:{$apiSecret}@{$cloudName}";
}

return [

    /*
    |--------------------------------------------------------------------------
    | Cloud Key (WAJIB ADA)
    |--------------------------------------------------------------------------
    | Bagian ini yang sebelumnya hilang dan bikin error 500.
    | Library Cloudinary mewajibkan adanya key 'cloud' ini.
     */
    'cloud' => [
        'cloud_name' => $cloudName,
        'api_key' => $apiKey,
        'api_secret' => $apiSecret,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cloud URL
    |--------------------------------------------------------------------------
     */
    'cloud_url' => $generatedUrl,

    /*
    |--------------------------------------------------------------------------
    | Upload Preset
    |--------------------------------------------------------------------------
     */
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),

    /*
    |--------------------------------------------------------------------------
    | Notification URL
    |--------------------------------------------------------------------------
     */
    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),

    /*
    |--------------------------------------------------------------------------
    | Secure URL
    |--------------------------------------------------------------------------
     */
    'secure_url' => env('CLOUDINARY_SECURE_URL', true),

    // Tambahan untuk kompatibilitas
    'url' => [
        'secure' => env('CLOUDINARY_SECURE_URL', true),
    ],
];
