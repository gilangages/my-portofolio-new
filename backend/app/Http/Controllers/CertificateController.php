<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCertificateRequest;
use App\Http\Requests\UpdateCertificateRequest;
use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::orderBy('created_at', 'desc')->get();

        // Transformasi path menjadi full URL agar bisa diakses frontend
        // Storage::url() otomatis menghandle local vs cloudinary URL
        $certificates->transform(function ($cert) {
            $cert->image_url = $cert->image_path ? Storage::url($cert->image_path) : null;
            return $cert;
        });

        return response()->json($certificates);
    }

    public function store(StoreCertificateRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // HAPUS parameter kedua 'public'. Biarkan Laravel memilih disk dari .env
            $data['image_path'] = $request->file('image')->store('certificates');
        }

        $certificate = Certificate::create($data);

        // Load URL untuk response
        $certificate->image_url = $certificate->image_path ? Storage::url($certificate->image_path) : null;

        return response()->json(['message' => 'Certificate created', 'data' => $certificate], 201);
    }

    public function update(UpdateCertificateRequest $request, $id)
    {
        $certificate = Certificate::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Hapus file lama (otomatis cek disk yang aktif di .env)
            if ($certificate->image_path) {
                Storage::delete($certificate->image_path);
            }

            // Upload file baru ke disk yang aktif
            $data['image_path'] = $request->file('image')->store('certificates');
        }

        $certificate->update($data);

        // Refresh model dan tambahkan URL
        $certificate->refresh();
        $certificate->image_url = $certificate->image_path ? Storage::url($certificate->image_path) : null;

        return response()->json(['message' => 'Certificate updated', 'data' => $certificate]);
    }

    public function show($id)
    {
        $certificate = Certificate::findOrFail($id);

        // Tambahkan URL gambar
        $certificate->image_url = $certificate->image_path ? Storage::url($certificate->image_path) : null;

        return response()->json($certificate);
    }

    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);

        // Hapus file dari disk aktif
        if ($certificate->image_path) {
            Storage::delete($certificate->image_path);
        }

        $certificate->delete();
        return response()->json(['message' => 'Certificate deleted']);
    }
}
