<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    /**
     * Store a newly created resource in storage it doesn't already exist.
     */
    public function store(Request $request)
    {
        // No request validation as per user request
        if (!$request->device_id) {
            return response()->json(['message' => 'device_id is required'], 400);
        }

        $visitor = Visitor::firstOrCreate(
            ['device_id' => $request->device_id]
        );

        return response()->json([
            'message' => 'Visitor logged successfully.',
            'data' => $visitor
        ], 201);
    }

    /**
     * Get the total count of visitors.
     */
    public function count()
    {
        $count = Visitor::count();

        return response()->json([
            'message' => 'Visitor count retrieved successfully',
            'data' => [
                'total_visitors' => $count
            ]
        ], 200);
    }
}
