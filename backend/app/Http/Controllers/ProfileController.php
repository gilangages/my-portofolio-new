<?php
namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // app/Http/Controllers/ProfileController.php

    public function update(Request $request)
    {
        // Ambil data pertama, atau buat baru
        $profile = Profile::firstOrNew(['id' => 1]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            // HAPUS 'public'. Gunakan default disk.
            // SEBELUM: ->store('profile', 'public');
            // SESUDAH:
            $data['photo_path'] = $request->file('photo')->store('profile');
        }

        if ($request->hasFile('cv')) {
            // HAPUS 'public'.
            $data['cv_path'] = $request->file('cv')->store('cv');
        }

        $profile->fill($data)->save();

        // Tambahkan URL lengkap untuk response frontend
        // Pastikan kamu import: use Illuminate\Support\Facades\Storage;
        $profile->photo_url = $profile->photo_path ? Storage::url($profile->photo_path) : null;
        $profile->cv_url = $profile->cv_path ? Storage::url($profile->cv_path) : null;

        return response()->json(['message' => 'Profile updated', 'data' => $profile]);
    }

    public function index()
    {
        $profile = Profile::first();
        $contacts = Contact::all();

        if ($profile) {
            $profile->photo_url = $profile->photo_path ? Storage::url($profile->photo_path) : null;
            $profile->cv_url = $profile->cv_path ? Storage::url($profile->cv_path) : null;
        }

        return response()->json([
            'about' => $profile,
            'social_media' => $contacts,
        ]);
    }
}
