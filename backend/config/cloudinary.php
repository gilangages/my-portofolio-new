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

    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),

    'cloud_url' => env('CLOUDINARY_URL'),

    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),

    'secure_url' => env('CLOUDINARY_SECURE_URL', true),
];
