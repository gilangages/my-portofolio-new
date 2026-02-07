<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSkillRequest;
use App\Http\Requests\UpdateSkillRequest;
use App\Models\Skill;
use Illuminate\Support\Facades\Storage;

class SkillController extends Controller
{
    public function index()
    {
        return response()->json(Skill::all());
    }

    public function store(StoreSkillRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('icon')) {
            $data['icon_path'] = $request->file('icon')->store('skills', 'public');
        }

        $skill = Skill::create($data);

        return response()->json(['message' => 'Skill created', 'data' => $skill], 201);
    }

    public function update(UpdateSkillRequest $request, $id)
    {
        $skill = Skill::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('icon')) {
            // Hapus icon lama
            if ($skill->icon_path) {
                Storage::disk('public')->delete($skill->icon_path);
            }

            $data['icon_path'] = $request->file('icon')->store('skills', 'public');
        }

        $skill->update($data);

        return response()->json(['message' => 'Skill updated', 'data' => $skill]);
    }

    public function destroy($id)
    {
        $skill = Skill::findOrFail($id);
        if ($skill->icon_path) {
            Storage::disk('public')->delete($skill->icon_path);
        }

        $skill->delete();

        return response()->json(['message' => 'Skill deleted']);
    }
}
