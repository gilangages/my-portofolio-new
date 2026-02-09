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
        // Tips: Saat return JSON, pastikan URL gambarnya lengkap (full path)
        $projects = Project::with('skills')->get();

        // Transformasi agar frontend dapat URL yang benar baik dari local maupun cloudinary
        $projects->transform(function ($project) {
            $project->thumbnail_url = $project->thumbnail_path ? Storage::url($project->thumbnail_path) : null;
            return $project;
        });

        return response()->json($projects);
    }

    public function show($id)
    {
        $project = Project::with('skills')->findOrFail($id);
        $project->thumbnail_url = $project->thumbnail_path ? Storage::url($project->thumbnail_path) : null;
        return response()->json($project);
    }

    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            // HAPUS parameter kedua 'public'. Biarkan Laravel memilih disk dari .env
            // Ini akan simpan ke 'projects/namafile.jpg' di disk yang aktif
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
            // Hapus file lama (Storage::delete otomatis cek disk yang aktif)
            if ($project->thumbnail_path) {
                Storage::delete($project->thumbnail_path);
            }

            // Upload baru tanpa parameter 'public'
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

        // Hapus file dari disk aktif (Cloudinary di prod, Local di dev)
        if ($project->thumbnail_path) {
            Storage::delete($project->thumbnail_path);
        }

        $project->delete();
        return response()->json(['message' => 'Project deleted']);
    }
}
