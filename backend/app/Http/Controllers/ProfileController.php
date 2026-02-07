<?php
namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    // Public: Get Profile + Contacts
    public function index()
    {
        $profile = Profile::first();
        $contacts = Contact::all();

        return response()->json([
            'about' => $profile,
            'social_media' => $contacts,
        ]);
    }

    // Admin: Update Profile
    public function update(Request $request)
    {
        // Ambil data pertama, atau buat baru jika belum ada
        $profile = Profile::firstOrNew(['id' => 1]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('profile', 'public');
        }
        if ($request->hasFile('cv')) {
            $data['cv_path'] = $request->file('cv')->store('cv', 'public');
        }

        $profile->fill($data)->save();

        return response()->json(['message' => 'Profile updated', 'data' => $profile]);
    }
}
