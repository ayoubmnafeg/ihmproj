@extends('layouts.admin')

@section('title', 'Admin Categories')
@section('admin_title', 'Category Management')
@section('admin_subtitle', 'Create and maintain content categories.')

@section('admin_content')
    <div class="card border-0 shadow-sm rounded-3 p-3 mb-3">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="row g-2">
            @csrf
            <div class="col-md-9">
                <input
                    type="text"
                    name="name"
                    class="form-control"
                    placeholder="New category name"
                    required
                >
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Add Category</button>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-3 p-3">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="d-flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="text" name="name" class="form-control form-control-sm" value="{{ $category->name }}" required>
                                    <button class="btn btn-sm btn-light">Save</button>
                                </form>
                            </td>
                            <td>{{ $category->created_at->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-muted">No categories yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
