@extends('layouts.admin')

@section('title', 'Admin User Detail')
@section('admin_title', 'User Detail')
@section('admin_subtitle', 'Inspect account history and moderation actions.')

@section('admin_content')
    <div class="card border-0 shadow-sm rounded-3 p-3 mb-3">
        <h5 class="fw-700 mb-3">{{ $user->profile->display_name ?? 'unknown_user' }}</h5>
        <p class="mb-1"><strong>Email:</strong> {{ $user->email }}</p>
        <p class="mb-1"><strong>Status:</strong> {{ $user->status }}</p>
        <p class="mb-0"><strong>Joined:</strong> {{ $user->created_at->format('Y-m-d H:i') }}</p>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100">
                <h6 class="fw-700 mb-2">Issue Warning</h6>
                <form method="POST" action="{{ route('admin.users.warn', $user) }}">
                    @csrf
                    <textarea name="reason" class="form-control mb-2" rows="3" placeholder="Warning reason" required></textarea>
                    <button type="submit" class="btn btn-sm btn-warning">Send Warning</button>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100">
                <h6 class="fw-700 mb-2">Ban Controls</h6>
                @if($user->status !== 'banned')
                    <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                        @csrf
                        <textarea name="reason" class="form-control mb-2" rows="3" placeholder="Ban reason" required></textarea>
                        <button type="submit" class="btn btn-sm btn-danger">Ban User</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.users.unban', $user) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-success">Unban User</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-3">
                <h6 class="fw-700 mb-2">Ban History</h6>
                <ul class="mb-0 ps-3">
                    @forelse($user->bans as $ban)
                        <li class="mb-1">{{ $ban->reason ?? 'No reason' }} ({{ $ban->created_at->format('Y-m-d') }})</li>
                    @empty
                        <li class="text-muted">No bans recorded.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-3">
                <h6 class="fw-700 mb-2">Warnings</h6>
                <ul class="mb-0 ps-3">
                    @forelse($user->warnings as $warning)
                        <li class="mb-1">{{ $warning->reason ?? $warning->message ?? 'No message' }} ({{ $warning->created_at->format('Y-m-d') }})</li>
                    @empty
                        <li class="text-muted">No warnings recorded.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
