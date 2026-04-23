<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @livewireStyles
    @yield('styles')
    <style>
        .admin-shell {
            min-height: 100vh;
            display: flex;
            background: #f5f7fb;
        }
        .admin-sidebar {
            width: 260px;
            background: #0f172a;
            color: #cbd5e1;
            padding: 24px 16px;
        }
        .admin-sidebar .brand {
            color: #fff;
            font-weight: 700;
            font-size: 22px;
            text-decoration: none;
            display: block;
            margin-bottom: 24px;
        }
        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            color: #cbd5e1;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .admin-nav-link:hover,
        .admin-nav-link.active {
            background: #1e293b;
            color: #fff;
        }
        .admin-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .admin-topbar {
            height: 72px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
        }
        .admin-content {
            padding: 24px;
        }
    </style>
</head>
<body>
<div class="admin-shell">
    <aside class="admin-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="brand">Admin Panel</a>

        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="feather-home"></i> Dashboard
        </a>
        <a href="{{ route('admin.users.index') }}" class="admin-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="feather-users"></i> Users
        </a>
        <a href="{{ route('admin.reports.index') }}" class="admin-nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="feather-flag"></i> Reports
        </a>
        <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="feather-grid"></i> Categories
        </a>
        <a href="{{ route('admin.moderators.index') }}" class="admin-nav-link {{ request()->routeIs('admin.moderators.*') ? 'active' : '' }}">
            <i class="feather-shield"></i> Moderators
        </a>
        <div class="mt-4 pt-3 border-top border-secondary">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="admin-nav-link border-0 bg-transparent w-100 text-start">
                    <i class="feather-log-out"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="admin-main">
        <div class="admin-topbar">
            <div>
                <h4 class="mb-0">@yield('admin_title', 'Admin Dashboard')</h4>
                <small class="text-muted">@yield('admin_subtitle', 'Manage platform operations from one place.')</small>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-primary">Dashboard Home</a>
        </div>

        <div class="admin-content">
            @if(session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger mb-3">{{ session('error') }}</div>
            @endif

            @yield('admin_content')
        </div>
    </main>
</div>

<script src="{{ asset('js/plugin.js') }}"></script>
<script src="{{ asset('js/scripts.js') }}"></script>
@livewireScripts
@yield('scripts')
</body>
</html>
