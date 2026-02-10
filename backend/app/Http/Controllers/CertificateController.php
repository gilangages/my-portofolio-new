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
        // image_url otomatis disertakan berkat properti $appends di Model
        $certificates = Certificate::orderBy('created_at', 'desc')->get();
        return response()->json($certificates);
    }

    public function store(StoreCertificateRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('certificates');
        }

        $certificate = Certificate::create($data);

        return response()->json([
            'message' => 'Certificate created',
            'data' => $certificate,
        ], 201);
    }

    public function update(UpdateCertificateRequest $request, $id)
    {
        $certificate = Certificate::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Hapus file lama jika ada
            if ($certificate->image_path) {
                Storage::delete($certificate->image_path);
            }
            $data['image_path'] = $request->file('image')->store('certificates');
        }

        $certificate->update($data);

        return response()->json([
            'message' => 'Certificate updated',
            'data' => $certificate->fresh(), // Mengambil data terbaru termasuk URL gambar baru
        ]);
    }

    public function show($id)
    {
        $certificate = Certificate::findOrFail($id);
        return response()->json($certificate);
    }

    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);

        if ($certificate->image_path) {
            Storage::delete($certificate->image_path);
        }

        $certificate->delete();
        return response()->json(['message' => 'Certificate deleted']);
    }
}
