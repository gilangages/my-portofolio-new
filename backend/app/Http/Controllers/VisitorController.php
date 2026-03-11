<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

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

        $agent = new Agent();

        $deviceType = 'desktop';
        if ($agent->isTablet()) {
            $deviceType = 'tablet';
        } elseif ($agent->isMobile()) {
            $deviceType = 'mobile';
        } elseif ($agent->isRobot()) {
            $deviceType = 'robot';
        }

        $visitor = Visitor::firstOrCreate(
            ['device_id' => $request->device_id],
            [
                'device_type' => $deviceType,
                'os'          => $agent->platform() ?: null,
                'browser'     => $agent->browser() ?: null,
                'device_name' => $agent->device() ?: null,
            ]
        );

        return response()->json([
            'message' => 'Visitor logged successfully.',
            'data' => $visitor
        ], 201);
    }

    /**
     * Get all visitors (Admin Only)
     */
    public function index()
    {
        $visitors = Visitor::orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Visitors retrieved successfully',
            'data' => $visitors
        ], 200);
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
