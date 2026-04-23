@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('admin_title', 'Admin Dashboard')
@section('admin_subtitle', 'Overview of platform health, recent activity, and moderation backlog.')

@section('admin_content')
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-3">
                <div class="text-muted small">Total Users</div>
                <div class="fw-700 font-xl">{{ number_format($kpis['users']) }}</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-3">
                <div class="text-muted small">Publications</div>
                <div class="fw-700 font-xl">{{ number_format($kpis['publications']) }}</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-3">
                <div class="text-muted small">Comments</div>
                <div class="fw-700 font-xl">{{ number_format($kpis['comments']) }}</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-3">
                <div class="text-muted small">Pending Reports</div>
                <div class="fw-700 font-xl">{{ number_format($kpis['pending_reports']) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('admin.users.index') }}" class="card border-0 shadow-sm rounded-3 p-3 d-block text-decoration-none">
                <div class="fw-700 text-dark">Manage Users</div>
                <div class="text-muted small">Review profiles, warnings, and bans.</div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('admin.reports.index') }}" class="card border-0 shadow-sm rounded-3 p-3 d-block text-decoration-none">
                <div class="fw-700 text-dark">Review Reports</div>
                <div class="text-muted small">Process moderation reports quickly.</div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('admin.categories.index') }}" class="card border-0 shadow-sm rounded-3 p-3 d-block text-decoration-none">
                <div class="fw-700 text-dark">Categories</div>
                <div class="text-muted small">Create and organize category structure.</div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('admin.moderators.index') }}" class="card border-0 shadow-sm rounded-3 p-3 d-block text-decoration-none">
                <div class="fw-700 text-dark">Moderators</div>
                <div class="text-muted small">Assign and manage moderator roles.</div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('admin.analytics.index') }}" class="card border-0 shadow-sm rounded-3 p-3 d-block text-decoration-none">
                <div class="fw-700 text-dark">Analytics</div>
                <div class="text-muted small">View historical snapshot analytics.</div>
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100">
                <h5 class="fw-700 mb-3">Recent Users</h5>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers as $member)
                                <tr>
                                    <td>{{ $member->display_name ?? 'unknown_user' }}</td>
                                    <td>{{ $member->email }}</td>
                                    <td>
                                        <span class="badge {{ $member->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $member->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted">No users yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100">
                <h5 class="fw-700 mb-3">Recent Reports</h5>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Reporter</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentReports as $report)
                                <tr>
                                    <td>{{ $report->reporter_name ?? 'unknown' }}</td>
                                    <td>{{ $report->reason }}</td>
                                    <td><span class="badge bg-secondary">{{ $report->status }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted">No reports yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3 p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-700 mb-0">Moderation Queue</h5>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-primary">Open Reports</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Report ID</th>
                                <th>Reason</th>
                                <th>Target</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingReports as $pending)
                                <tr>
                                    <td>{{ $pending->id }}</td>
                                    <td>{{ $pending->reason }}</td>
                                    <td>{{ $pending->target_id }}</td>
                                    <td>{{ $pending->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted">No pending reports.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
