<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request, Content $content): JsonResponse
    {
        $data = $request->validate([
            'reason'  => 'required|string|in:spam,harassment,misinformation,hate_speech,other',
            'details' => 'nullable|string|max:1000',
        ]);

        $report = Report::create([
            'reason'      => $data['reason'],
            'details'     => $data['details'] ?? null,
            'status'      => 'pending',
            'reporter_id' => $request->user()->id,
            'target_id'   => $content->id,
        ]);

        return response()->json($report, 201);
    }

    // Moderator: list all pending reports
    public function index(Request $request): JsonResponse
    {
        $reports = Report::with(['reporter.profile', 'target', 'reviewer.profile'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return response()->json($reports);
    }

    // Moderator: update report status
    public function update(Request $request, Report $report): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|string|in:reviewed,dismissed,actioned',
        ]);

        $report->update([
            'status'      => $data['status'],
            'reviewed_by' => $request->user()->id,
        ]);

        return response()->json($report->load(['reporter.profile', 'target', 'reviewer.profile']));
    }
}
