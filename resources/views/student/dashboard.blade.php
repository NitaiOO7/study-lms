@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-section">
            <div class="sidebar-title">Menu</div>
            <a href="{{ route('student.dashboard') }}" class="sidebar-link active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="{{ route('student.analytics') }}" class="sidebar-link"><i class="fas fa-chart-pie"></i> Performance Analytics</a>
            <a href="{{ route('student.browse') }}" class="sidebar-link"><i class="fas fa-compass"></i> Browse Courses</a>
            <a href="{{ route('student.my-courses') }}" class="sidebar-link"><i class="fas fa-book-reader"></i> My Learning</a>
        </div>
        <div class="sidebar-section">
            <div class="sidebar-title">Community</div>
            <a href="{{ route('community.index') }}" class="sidebar-link"><i class="fas fa-comments"></i> Discussion Forums</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header animate-in">
            <div>
                <h1 class="page-title">Hi, {{ auth()->user()->name }}! 👋</h1>
                <p class="page-subtitle">Ready to continue your learning journey?</p>
            </div>
        </div>

        <div class="grid-3 animate-in delay-1">
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--gradient-primary);"><i class="fas fa-book"></i></div>
                <div>
                    <div class="stat-value">{{ $activeSubscriptions->count() }}</div>
                    <div class="stat-label">Active Courses</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--gradient-success);"><i class="fas fa-clipboard-check"></i></div>
                <div>
                    <div class="stat-value">{{ $totalTests }}</div>
                    <div class="stat-label">Tests Completed</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--gradient-accent);"><i class="fas fa-chart-line"></i></div>
                <div>
                    <div class="stat-value">{{ number_format($avgScore, 1) }}%</div>
                    <div class="stat-label">Average Score</div>
                </div>
            </div>
        </div>

        <div style="margin-top: 32px;" class="animate-in delay-2">
            <h2 class="section-title" style="font-size: 1.3rem;">Continue Learning</h2>
            @if($activeSubscriptions->count() > 0)
                <div class="grid-3 mt-3">
                    @foreach($activeSubscriptions->take(3) as $sub)
                        <div class="course-card">
                            <div class="course-thumb" style="height: 120px;">
                                @if($sub->course->thumbnail)
                                    <img src="{{ Storage::url($sub->course->thumbnail) }}" alt="{{ $sub->course->title }}">
                                @else
                                    <i class="fas fa-book-open"></i>
                                @endif
                                <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 4px; background: rgba(255,255,255,0.2);">
                                    <div style="width: 35%; height: 100%; background: var(--success);"></div>
                                </div>
                            </div>
                            <div class="course-body" style="padding: 16px;">
                                <h3 class="course-title" style="font-size: 0.95rem; margin-bottom: 4px;">{{ Str::limit($sub->course->title, 40) }}</h3>
                                <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 12px;">{{ $sub->course->channel->name }}</p>
                                <div style="display: flex; justify-content: space-between; gap: 8px;">
                                    <a href="{{ route('student.materials', $sub->course->slug) }}" class="btn btn-secondary btn-sm" style="flex:1; padding: 6px;"><i class="fas fa-file-alt"></i> Material</a>
                                    <a href="{{ route('student.test-series', $sub->course->slug) }}" class="btn btn-primary btn-sm" style="flex:1; padding: 6px;"><i class="fas fa-pen"></i> Tests</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state card" style="padding: 30px;">
                    <i class="fas fa-graduation-cap"></i>
                    <h3 style="font-size: 1.1rem;">Not enrolled in any courses</h3>
                    <p style="font-size: 0.85rem; margin-bottom: 20px;">Start your learning journey by exploring our premium courses.</p>
                    <a href="{{ route('student.browse') }}" class="btn btn-primary">Browse Courses</a>
                </div>
            @endif
        </div>

        <div style="margin-top: 32px;" class="animate-in delay-3">
            <h2 class="section-title" style="font-size: 1.3rem;">Recent Test Attempts</h2>
            @if($recentAttempts->count() > 0)
                <div class="card mt-3">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Test Series</th>
                                    <th>Section</th>
                                    <th>Score</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAttempts as $attempt)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600;">{{ Str::limit($attempt->testSeries->title, 30) }}</div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y') : 'Started ' . $attempt->started_at->diffForHumans() }}</div>
                                        </td>
                                        <td>{{ Str::limit($attempt->section->title, 25) }}</td>
                                        <td>
                                            @if($attempt->status === 'completed')
                                                <span style="font-weight: 700; color: {{ $attempt->percentage >= $attempt->section->passing_marks ? 'var(--success)' : 'var(--danger)' }}">{{ $attempt->score }} / {{ $attempt->total_marks }}</span>
                                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $attempt->percentage }}%</div>
                                            @else
                                                <span class="badge badge-warning">--</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attempt->status === 'completed')
                                                <span class="badge badge-success">Completed</span>
                                            @elseif($attempt->status === 'in_progress')
                                                <span class="badge badge-warning">In Progress</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attempt->status === 'completed')
                                                <a href="{{ route('student.test-report', $attempt->id) }}" class="btn btn-secondary btn-sm">View Report</a>
                                            @else
                                                <a href="{{ route('student.start-test', $attempt->section->id) }}" class="btn btn-primary btn-sm">Resume</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="card mt-3" style="text-align: center; padding: 30px;">
                    <p style="color: var(--text-muted); font-size: 0.9rem;">You haven't attempted any tests yet.</p>
                </div>
            @endif
        </div>
    </main>
</div>
@endsection
