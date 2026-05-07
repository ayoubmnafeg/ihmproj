<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Publication;
use App\Models\Report;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $kpis = [
            'users' => User::count(),
            'publications' => Publication::count(),
            'comments' => Comment::count(),
            'reports' => Report::count(),
            'pending_reports' => Report::query()->where('status', 'pending')->count(),
        ];

        $recentUsers = User::query()
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->select('users.id', 'users.email', 'users.status', 'users.created_at', 'profiles.display_name')
            ->latest('users.created_at')
            ->take(5)
            ->get();

        $recentReports = Report::query()
            ->leftJoin('profiles as reporter_profiles', 'reporter_profiles.user_id', '=', 'reports.reporter_id')
            ->select(
                'reports.id',
                'reports.reason',
                'reports.status',
                'reports.created_at',
                'reports.target_id',
                'reporter_profiles.display_name as reporter_name'
            )
            ->latest('reports.created_at')
            ->take(8)
            ->get();

        $pendingReports = Report::query()
            ->where('status', 'pending')
            ->latest('created_at')
            ->take(8)
            ->get(['id', 'reason', 'created_at', 'target_id']);

        return view('admin.dashboard', compact('kpis', 'recentUsers', 'recentReports', 'pendingReports'));
    }
}
