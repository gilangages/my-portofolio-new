<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;

// use Illuminate\Support\Facades\Storage; // HAPUS ATAU KOMENTAR BARIS INI (Gak butuh lagi)

class ContactController extends Controller
{
    public function index()
    {
        return response()->json(Contact::all());
    }

    public function store(StoreContactRequest $request)
    {
        // 1. Ambil data (platform_name, url, icon)
        $data = $request->validated();

        // 2. Langsung simpan (karena icon cuma text string, gak perlu upload)
        $contact = Contact::create($data);

        return response()->json(['message' => 'Contact created', 'data' => $contact], 201);
    }

    public function show($id)
    {
        return response()->json(Contact::findOrFail($id));
    }

    public function update(UpdateContactRequest $request, $id)
    {
        $contact = Contact::findOrFail($id);
        $data = $request->validated();

        // 3. Update langsung datanya
        $contact->update($data);

        return response()->json(['message' => 'Contact updated', 'data' => $contact]);
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);

        // 4. Hapus data di DB saja (gak perlu hapus file di storage)
        $contact->delete();

        return response()->json(['message' => 'Contact deleted']);
    }
}
