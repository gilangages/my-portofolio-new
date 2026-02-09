<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi utama Cloudinary. Pastikan CLOUDINARY_URL ada di .env
    |
     */

    'cloud_url' => env('CLOUDINARY_URL'),

    'cloud' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key' => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
    ],

    'url' => [
        'secure' => env('CLOUDINARY_SECURE_URL', true),
    ],

    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),

    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),

    'secure_url' => env('CLOUDINARY_SECURE_URL', true),
];
