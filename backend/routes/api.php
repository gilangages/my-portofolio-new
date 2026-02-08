<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SkillController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Public Routes (Dapat diakses siapa saja/Frontend Portfolio)
|--------------------------------------------------------------------------
 */
Route::post('/admin/login', [AuthController::class, 'login']);

Route::get('/profile', [ProfileController::class, 'index']);
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/skills', [SkillController::class, 'index']); // Asumsi kamu buat SkillController
Route::get('/experiences', [ExperienceController::class, 'index']); // Asumsi kamu buat ExperienceController
Route::get('/certificates', [CertificateController::class, 'index']); // Asumsi kamu buat CertificateController
Route::get('/contacts', [ContactController::class, 'index']); // Asumsi kamu buat ContactController
Route::get('/services', [ServiceController::class, 'index']);

// Show Single (Detail untuk Edit) - TAMBAHKAN INI
Route::get('/projects/{id}', [ProjectController::class, 'show']);
Route::get('/skills/{id}', [SkillController::class, 'show']);
Route::get('/experiences/{id}', [ExperienceController::class, 'show']);
Route::get('/certificates/{id}', [CertificateController::class, 'show']);
Route::get('/contacts/{id}', [ContactController::class, 'show']);
Route::get('/services/{id}', [ServiceController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Hanya Admin - Butuh Token Bearer)
|--------------------------------------------------------------------------
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/admin/logout', [AuthController::class, 'logout']);

    // Profile (Update only)
    Route::post('/profile', [ProfileController::class, 'update']); // Menggunakan POST untuk support file upload dengan mudah

    // Projects CRUD
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::put('/projects/{id}', [ProjectController::class, 'update']); // Note: Laravel kadang butuh _method: PUT di FormData untuk file
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);

    // Skills CRUD
    Route::post('/skills', [SkillController::class, 'store']);
    Route::put('/skills/{id}', [SkillController::class, 'update']);
    Route::delete('/skills/{id}', [SkillController::class, 'destroy']);

    // Experiences CRUD
    Route::post('/experiences', [ExperienceController::class, 'store']);
    Route::put('/experiences/{id}', [ExperienceController::class, 'update']);
    Route::delete('/experiences/{id}', [ExperienceController::class, 'destroy']);

    // Certificates CRUD
    Route::post('/certificates', [CertificateController::class, 'store']);
    Route::put('/certificates/{id}', [CertificateController::class, 'update']);
    Route::delete('/certificates/{id}', [CertificateController::class, 'destroy']);

    // Contacts CRUD
    Route::post('/contacts', [ContactController::class, 'store']);
    Route::put('/contacts/{id}', [ContactController::class, 'update']);
    Route::delete('/contacts/{id}', [ContactController::class, 'destroy']);

    //Services CRUD
    Route::post('/services', [ServiceController::class, 'store']);
    Route::put('/services/{id}', [ServiceController::class, 'update']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
});
