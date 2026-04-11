@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-section">
            <div class="sidebar-title">Menu</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="{{ route('admin.users') }}" class="sidebar-link"><i class="fas fa-users"></i> Users</a>
            <a href="{{ route('admin.channels') }}" class="sidebar-link"><i class="fas fa-tv"></i> Channels</a>
            <a href="{{ route('admin.courses') }}" class="sidebar-link"><i class="fas fa-book"></i> Courses</a>
            <a href="{{ route('admin.subjects') }}" class="sidebar-link"><i class="fas fa-tags"></i> Subjects</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header animate-in">
            <div>
                <h1 class="page-title">Admin Dashboard</h1>
                <p class="page-subtitle">Platform overview and statistics.</p>
            </div>
        </div>

        <div class="grid-4 animate-in delay-1">
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--gradient-primary);"><i class="fas fa-user-graduate"></i></div>
                <div><div class="stat-value">{{ $stats['total_students'] }}</div><div class="stat-label">Students</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--gradient-accent);"><i class="fas fa-chalkboard-teacher"></i></div>
                <div><div class="stat-value">{{ $stats['total_teachers'] }}</div><div class="stat-label">Teachers</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #ec4899, #8b5cf6);"><i class="fas fa-tv"></i></div>
                <div><div class="stat-value">{{ $stats['total_channels'] }}</div><div class="stat-label">Channels</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--gradient-success);"><i class="fas fa-dollar-sign"></i></div>
                <div><div class="stat-value">${{ number_format($stats['total_revenue'], 0) }}</div><div class="stat-label">Total Revenue</div></div>
            </div>
        </div>
    </main>
</div>
@endsection
