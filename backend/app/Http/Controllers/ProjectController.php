<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index()
    {
        // Eager load skills biar performa bagus
        return response()->json(Project::with('skills')->get());
    }

    public function show($id)
    {
        return response()->json(Project::with('skills')->findOrFail($id));
    }

    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();

        // Handle File Upload
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('projects', 'public');
        }

        $project = Project::create($data);

        // Sync Relasi Many-to-Many (Skills)
        if ($request->has('tech_stack_ids')) {
            $project->skills()->sync($request->tech_stack_ids);
        }

        return response()->json(['message' => 'Project created', 'data' => $project->load('skills')], 201);
    }

    public function update(Request $request, $id)
    {
        // Note: Gunakan FormRequest terpisah untuk update jika validasi strict
        $project = Project::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('thumbnail')) {
            // Hapus file lama jika ada
            if ($project->thumbnail_path) {
                Storage::disk('public')->delete($project->thumbnail_path);
            }

            $data['thumbnail_path'] = $request->file('thumbnail')->store('projects', 'public');
        }

        $project->update($data);

        if ($request->has('tech_stack_ids')) {
            $project->skills()->sync($request->tech_stack_ids);
        }

        return response()->json(['message' => 'Project updated', 'data' => $project->load('skills')]);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        if ($project->thumbnail_path) {
            Storage::disk('public')->delete($project->thumbnail_path);
        }

        $project->delete();
        return response()->json(['message' => 'Project deleted']);
    }
}
