<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformStatistics;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(): View
    {
        $stats = PlatformStatistics::latest()->first();

        return view('admin.analytics', compact('stats'));
    }

    public function snapshot(): RedirectResponse
    {
        PlatformStatistics::create([
            'total_users' => \App\Models\User::count(),
            'total_publications' => \App\Models\Publication::count(),
            'total_comments' => \App\Models\Comment::count(),
            'total_reports' => \App\Models\Report::count(),
        ]);

        return back()->with('success', 'Snapshot taken.');
    }
}
