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
        // Accessor thumbnail_url otomatis disertakan
        $projects = Project::with('skills')->latest()->get();
        return response()->json($projects);
    }

    public function show($id)
    {
        $project = Project::with('skills')->findOrFail($id);
        return response()->json($project);
    }

    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('projects');
        }

        $project = Project::create($data);

        if ($request->has('tech_stack_ids')) {
            $project->skills()->sync($request->tech_stack_ids);
        }

        return response()->json(['message' => 'Project created', 'data' => $project->load('skills')], 201);
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('thumbnail')) {
            if ($project->thumbnail_path) {
                Storage::delete($project->thumbnail_path);
            }
            $data['thumbnail_path'] = $request->file('thumbnail')->store('projects');
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
            Storage::delete($project->thumbnail_path);
        }
        $project->delete();
        return response()->json(['message' => 'Project deleted']);
    }
}
