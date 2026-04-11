@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-section">
            <div class="sidebar-title">Menu</div>
            <a href="{{ route('teacher.dashboard') }}" class="sidebar-link active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="{{ route('teacher.courses') }}" class="sidebar-link"><i class="fas fa-book"></i> My Courses</a>
            <a href="{{ route('teacher.materials') }}" class="sidebar-link"><i class="fas fa-file-alt"></i> Study Materials</a>
            <a href="{{ route('teacher.test-series') }}" class="sidebar-link"><i class="fas fa-tasks"></i> Test Series</a>
        </div>
        <div class="sidebar-section">
            <div class="sidebar-title">Community</div>
            <a href="{{ route('community.index') }}" class="sidebar-link"><i class="fas fa-comments"></i> Forums</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header animate-in">
            <div>
                <h1 class="page-title">Welcome back, {{ auth()->user()->name }}!</h1>
                <p class="page-subtitle">Here is the overview of your channel.</p>
            </div>
            @if(!$channel->is_verified)
                <div class="badge badge-warning" style="padding: 10px 16px; font-size: 0.85rem;"><i class="fas fa-exclamation-triangle"></i> Channel Pending Verification</div>
            @else
                <div class="badge badge-success" style="padding: 10px 16px; font-size: 0.85rem;"><i class="fas fa-check-circle"></i> Verified Channel</div>
            @endif
        </div>

        <div class="grid-4 animate-in delay-1">
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--gradient-primary);"><i class="fas fa-book"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['total_courses'] }}</div>
                    <div class="stat-label">Total Courses</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--gradient-success);"><i class="fas fa-users"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['total_students'] }}</div>
                    <div class="stat-label">Enrolled Students</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--gradient-accent);"><i class="fas fa-file-video"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['total_materials'] }}</div>
                    <div class="stat-label">Study Materials</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #06b6d4);"><i class="fas fa-dollar-sign"></i></div>
                <div>
                    <div class="stat-value">${{ number_format($stats['total_revenue'], 2) }}</div>
                    <div class="stat-label">Total Revenue</div>
                </div>
            </div>
        </div>

        <div class="grid-2" style="margin-top: 32px;">
            <!-- Recent Subscriptions -->
            <div class="card animate-in delay-2">
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                    <h3 style="font-size: 1.1rem;">Recent Enrollments</h3>
                </div>
                @if($recentSubscriptions->count() > 0)
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSubscriptions as $sub)
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <div class="avatar" style="width: 32px; height: 32px; font-size: 0.75rem;">{{ substr($sub->student->name, 0, 1) }}</div>
                                                <div>
                                                    <div style="font-weight: 600; font-size: 0.9rem;">{{ $sub->student->name }}</div>
                                                    <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $sub->student->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ Str::limit($sub->course->title, 30) }}</td>
                                        <td>{{ $sub->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state py-4" style="padding: 30px;">
                        <i class="fas fa-users" style="font-size: 2rem;"></i>
                        <h4 style="font-size: 1rem;">No enrollments yet</h4>
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="card animate-in delay-3">
                <h3 style="font-size: 1.1rem; margin-bottom: 20px;">Quick Actions</h3>
                <div style="display: grid; gap: 15px;">
                    <a href="{{ route('teacher.courses.create') }}" class="btn btn-secondary" style="justify-content: flex-start; padding: 16px; background: var(--dark-surface);">
                        <div class="stat-icon" style="width: 40px; height: 40px; font-size: 1rem; background: rgba(99,102,241,0.1); color: var(--primary); margin-right: 10px;"><i class="fas fa-plus"></i></div>
                        <div>
                            <div style="font-weight: 600; color: var(--text-primary);">Create New Course</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Launch a new course subject</div>
                        </div>
                    </a>
                    <a href="{{ route('teacher.test-series.create') }}" class="btn btn-secondary" style="justify-content: flex-start; padding: 16px; background: var(--dark-surface);">
                        <div class="stat-icon" style="width: 40px; height: 40px; font-size: 1rem; background: rgba(245,158,11,0.1); color: var(--accent); margin-right: 10px;"><i class="fas fa-tasks"></i></div>
                        <div>
                            <div style="font-weight: 600; color: var(--text-primary);">Create Test Series</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Build mock tests for students</div>
                        </div>
                    </a>
                    <a href="{{ route('teacher.materials') }}" class="btn btn-secondary" style="justify-content: flex-start; padding: 16px; background: var(--dark-surface);">
                        <div class="stat-icon" style="width: 40px; height: 40px; font-size: 1rem; background: rgba(16,185,129,0.1); color: var(--success); margin-right: 10px;"><i class="fas fa-upload"></i></div>
                        <div>
                            <div style="font-weight: 600; color: var(--text-primary);">Upload Material</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Share PDFs and video lectures</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
