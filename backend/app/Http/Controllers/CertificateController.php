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
        return response()->json(Certificate::orderBy('created_at', 'desc')->get());
    }

    public function store(StoreCertificateRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('certificates', 'public');
        }

        $certificate = Certificate::create($data);
        return response()->json(['message' => 'Certificate created', 'data' => $certificate], 201);
    }

    public function update(UpdateCertificateRequest $request, $id)
    {
        $certificate = Certificate::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($certificate->image_path) {
                Storage::disk('public')->delete($certificate->image_path);
            }

            $data['image_path'] = $request->file('image')->store('certificates', 'public');
        }

        $certificate->update($data);
        return response()->json(['message' => 'Certificate updated', 'data' => $certificate]);
    }

    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);
        if ($certificate->image_path) {
            Storage::disk('public')->delete($certificate->image_path);
        }

        $certificate->delete();
        return response()->json(['message' => 'Certificate deleted']);
    }
}
