<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{
    public function index()
    {
        return response()->json(Contact::all());
    }

    public function store(StoreContactRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('icon')) {
            $data['icon_path'] = $request->file('icon')->store('contacts', 'public');
        }

        $contact = Contact::create($data);
        return response()->json(['message' => 'Contact created', 'data' => $contact], 201);
    }

    public function update(UpdateContactRequest $request, $id)
    {
        $contact = Contact::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('icon')) {
            if ($contact->icon_path) {
                Storage::disk('public')->delete($contact->icon_path);
            }

            $data['icon_path'] = $request->file('icon')->store('contacts', 'public');
        }

        $contact->update($data);
        return response()->json(['message' => 'Contact updated', 'data' => $contact]);
    }

    // Tambahkan di bawah index atau sebelum update
    public function show($id)
    {
        return response()->json(Contact::findOrFail($id));
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        if ($contact->icon_path) {
            Storage::disk('public')->delete($contact->icon_path);
        }

        $contact->delete();
        return response()->json(['message' => 'Contact deleted']);
    }
}
