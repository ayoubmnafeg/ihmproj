@extends('layouts.app')

@section('title', __('Moderation reports'))

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card shadow-xss w-100 d-block border-0 p-4 mb-3">
            <div class="card-body p-0">
                <h2 class="fw-700 mb-3 font-md text-grey-900">{{ __('Moderation reports') }}</h2>
                <p class="fw-500 font-xssss text-grey-500 mb-4">{{ __('Review and update report statuses.') }}</p>

                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Reporter') }}</th>
                                <th>{{ __('Reason') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th class="text-end">{{ __('Update') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                                <tr>
                                    <td>{{ $report->reporter->profile->display_name ?? __('unknown') }}</td>
                                    <td>{{ $report->reason }}</td>
                                    <td><span class="badge bg-secondary">{{ $report->status }}</span></td>
                                    <td>{{ $report->created_at->diffForHumans() }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('reports.update', $report) }}" class="d-inline-flex gap-2 align-items-center justify-content-end flex-wrap">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="form-select form-select-sm" style="max-width: 10rem;">
                                                <option value="pending" @selected($report->status === 'pending')>pending</option>
                                                <option value="reviewed" @selected($report->status === 'reviewed')>reviewed</option>
                                                <option value="dismissed" @selected($report->status === 'dismissed')>dismissed</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-light">{{ __('Save') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-muted">{{ __('No reports found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $reports->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
