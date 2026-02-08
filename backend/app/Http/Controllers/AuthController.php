<?php
namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        // 1. Ambil data yang SUDAH divalidasi otomatis oleh LoginRequest
        // Tidak perlu tulis ulang rules di sini
        $credentials = $request->validated();

        // 2. Cek Login
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // 3. Buat Token
            $token = $user->createToken('admin-token')->plainTextToken;
            return response()->json([
                'message' => 'Login successfully',
                'user' => $user,
                'token' => $token]);
        }
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
