<?php
namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        // 1. Ambil atau buat profile (Idempotent)
        $profile = Profile::firstOrNew(['id' => 1]);

        // 2. Siapkan data update (kecuali file untuk diproses manual)
        $data = $request->except(['photo', 'cv']);

        // 3. Handle Upload Photo
        if ($request->hasFile('photo')) {
            // Hapus file lama jika ada (menggunakan default disk)
            if ($profile->photo_path && Storage::exists($profile->photo_path)) {
                Storage::delete($profile->photo_path);
            }
            // SIMPAN: Jangan tentukan disk 'public', biarkan default config yang bekerja
            $data['photo_path'] = $request->file('photo')->store('profile');
        }

        // 4. Handle Upload CV
        if ($request->hasFile('cv')) {
            if ($profile->cv_path && Storage::exists($profile->cv_path)) {
                Storage::delete($profile->cv_path);
            }
            $data['cv_path'] = $request->file('cv')->store('cv');
        }

        // 5. Simpan ke Database
        $profile->fill($data)->save();

        // 6. Generate URL (menggunakan helper Storage::url yang otomatis menyesuaikan disk)
        $profile->photo_url = $profile->photo_path ? Storage::url($profile->photo_path) : null;
        $profile->cv_url = $profile->cv_path ? Storage::url($profile->cv_path) : null;

        return response()->json(['message' => 'Profile updated', 'data' => $profile]);
    }

    public function index()
    {
        $profile = Profile::first();
        $contacts = Contact::all();

        if ($profile) {
            // Generate URL dinamis berdasarkan disk yang aktif (Local/Cloudinary)
            $profile->photo_url = $profile->photo_path ? Storage::url($profile->photo_path) : null;
            $profile->cv_url = $profile->cv_path ? Storage::url($profile->cv_path) : null;
        }

        return response()->json([
            'about' => $profile,
            'social_media' => $contacts,
        ]);
    }
}
