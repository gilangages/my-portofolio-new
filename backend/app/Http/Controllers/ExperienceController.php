<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExperienceRequest;
use App\Http\Requests\UpdateExperienceRequest;
use App\Models\Experience;

class ExperienceController extends Controller
{
    public function index()
    {
        // Urutkan dari yang terbaru (start_date desc)
        return response()->json(Experience::orderBy('start_date', 'desc')->get());
    }

    public function store(StoreExperienceRequest $request)
    {
        $experience = Experience::create($request->validated());
        return response()->json(['message' => 'Experience created', 'data' => $experience], 201);
    }

    public function update(UpdateExperienceRequest $request, $id)
    {
        $experience = Experience::findOrFail($id);
        $experience->update($request->validated());
        return response()->json(['message' => 'Experience updated', 'data' => $experience]);
    }

    public function destroy($id)
    {
        Experience::findOrFail($id)->delete();
        return response()->json(['message' => 'Experience deleted']);
    }
}
