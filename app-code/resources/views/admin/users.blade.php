@extends('layouts.admin')

@section('title', 'Admin Users')
@section('admin_title', 'User Management')
@section('admin_subtitle', 'Review member accounts and take moderation actions.')

@section('admin_content')
    <div class="card border-0 shadow-sm rounded-3 p-3">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr>
                            <td>{{ $member->profile->display_name ?? 'unknown_user' }}</td>
                            <td>{{ $member->email }}</td>
                            <td>
                                <span class="badge {{ $member->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $member->status }}
                                </span>
                            </td>
                            <td>{{ $member->created_at->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.users.show', $member) }}" class="btn btn-sm btn-light">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $members->links() }}
        </div>
    </div>
@endsection
