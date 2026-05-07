<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $reports = Report::with(['target', 'reporter.profile'])
            ->latest()
            ->paginate(20);

        $view = request()->routeIs('admin.*')
            ? 'admin.reports'
            : 'moderator.reports';

        return view($view, compact('reports'));
    }

    public function store(Request $request, Content $content): RedirectResponse
    {
        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        Report::create([
            'content_id' => $content->id,
            'reporter_id' => $request->user()->id,
            'reason' => $data['reason'],
        ]);

        return back()->with('success', 'Report submitted.');
    }

    public function update(Request $request, Report $report): RedirectResponse
    {
        $data = $request->validate([
            'status' => 'required|string|in:pending,reviewed,dismissed',
        ]);

        $report->update($data);

        return back()->with('success', 'Report updated.');
    }
}
