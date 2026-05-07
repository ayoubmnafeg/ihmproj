@extends('layouts.admin')

@section('title', 'Admin Reports')
@section('admin_title', 'Moderation Reports')
@section('admin_subtitle', 'Review and update report statuses.')

@section('admin_content')
    <div class="card border-0 shadow-sm rounded-3 p-3">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Reporter</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end">Update</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            <td>{{ $report->reporter->profile->display_name ?? 'unknown' }}</td>
                            <td>{{ $report->reason }}</td>
                            <td><span class="badge bg-secondary">{{ $report->status }}</span></td>
                            <td>{{ $report->created_at->diffForHumans() }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.reports.update', $report) }}" class="d-inline-flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="pending" @selected($report->status === 'pending')>pending</option>
                                        <option value="reviewed" @selected($report->status === 'reviewed')>reviewed</option>
                                        <option value="dismissed" @selected($report->status === 'dismissed')>dismissed</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-light">Save</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted">No reports found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $reports->links() }}
        </div>
    </div>
@endsection
