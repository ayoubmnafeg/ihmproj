<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\PlatformStatistics;
use App\Models\Publication;
use App\Models\Report;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(PlatformStatistics::latest('recorded_at')->paginate(30));
    }

    public function snapshot(): JsonResponse
    {
        $stat = PlatformStatistics::create([
            'total_publications' => Publication::count(),
            'total_reports'      => Report::count(),
            'total_responses'    => Comment::count(),
            'recorded_at'        => now(),
        ]);

        return response()->json($stat, 201);
    }
}
