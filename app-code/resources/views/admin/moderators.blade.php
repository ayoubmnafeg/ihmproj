@extends('layouts.admin')

@section('title', 'Admin Moderators')
@section('admin_title', 'Moderator Roles')
@section('admin_subtitle', 'Assign or remove moderator privileges.')

@section('admin_content')
    <div class="card border-0 shadow-sm rounded-3 p-3 mb-3">
        <form method="POST" action="{{ route('admin.moderators.assign') }}" class="row g-2">
            @csrf
            <div class="col-md-9">
                <select name="user_id" class="form-select" required>
                    <option value="">Select a user</option>
                    @foreach($assignableUsers as $candidate)
                        <option value="{{ $candidate->id }}">
                            {{ $candidate->profile->display_name ?? 'unknown_user' }} ({{ $candidate->email }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Assign Moderator</button>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-3 p-3">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Assigned Since</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($moderators as $moderator)
                        <tr>
                            <td>{{ $moderator->user->profile->display_name ?? 'unknown_user' }}</td>
                            <td>{{ $moderator->user->email }}</td>
                            <td>{{ optional($moderator->assigned_since)->format('Y-m-d') ?? $moderator->created_at->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.moderators.remove', $moderator->user) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted">No moderators assigned.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
