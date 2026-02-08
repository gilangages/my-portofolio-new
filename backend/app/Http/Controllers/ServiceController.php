<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->query('all') === 'true') {
            return response()->json(Service::all());
        }
        // Publik hanya melihat yang aktif
        return response()->json(Service::where('is_active', true)->get());

    }

    public function show($id)
    {
        return response()->json(Service::findOrFail($id));
    }

    public function store(StoreServiceRequest $request)
    {
        $service = Service::create($request->validated());
        return response()->json(['message' => 'Service created', 'data' => $service], 201);
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $service->update($request->all());
        return response()->json(['message' => 'Service updated', 'data' => $service]);
    }

    public function destroy($id)
    {
        Service::findOrFail($id)->delete();
        return response()->json(['message' => 'Service deleted']);
    }
}
