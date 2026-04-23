@extends('layouts.admin')

@section('title', 'Admin Analytics')
@section('admin_title', 'Analytics Snapshot')
@section('admin_subtitle', 'Latest saved platform metrics.')

@section('admin_content')
    <div class="card border-0 shadow-sm rounded-3 p-3 mb-3">
        <form method="POST" action="{{ route('admin.statistics.snapshot') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Take New Snapshot</button>
        </form>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3">
                <div class="text-muted small">Total Publications</div>
                <div class="fw-700 font-xl">{{ number_format($stats->total_publications ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3">
                <div class="text-muted small">Total Reports</div>
                <div class="fw-700 font-xl">{{ number_format($stats->total_reports ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3">
                <div class="text-muted small">Total Responses</div>
                <div class="fw-700 font-xl">{{ number_format($stats->total_responses ?? 0) }}</div>
            </div>
        </div>
    </div>
@endsection
