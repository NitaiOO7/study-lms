@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-section">
            <div class="sidebar-title">Menu</div>
            <a href="{{ route('student.dashboard') }}" class="sidebar-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="{{ route('student.my-courses') }}" class="sidebar-link active"><i class="fas fa-book-reader"></i> My Learning</a>
            <a href="{{ route('student.course.detail', $course->slug) }}" class="sidebar-link"><i class="fas fa-info-circle"></i> Course Info</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header animate-in">
            <div>
                <h1 class="page-title">Test Series</h1>
                <p class="page-subtitle">{{ $course->title }}</p>
            </div>
            @if(!$isSubscribed)
                <div class="badge badge-warning" style="padding: 10px 16px;"><i class="fas fa-exclamation-triangle"></i> Not Enrolled - Showing Demos Only</div>
            @endif
        </div>

        @if($isSubscribed && $testSeries->count() > 0)
            <div class="grid-2 animate-in delay-1">
                @foreach($testSeries as $series)
                    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                                <div style="font-size: 2.5rem; color: var(--primary);"><i class="fas fa-tasks"></i></div>
                                @if($series->is_demo)
                                    <div class="badge badge-success">Demo</div>
                                @endif
                            </div>
                            <h3 style="font-size: 1.2rem; margin-bottom: 8px;">{{ $series->title }}</h3>
                            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 20px;">{{ Str::limit($series->description, 100) }}</p>
                            <div style="display: flex; gap: 16px; margin-bottom: 24px; font-size: 0.85rem; color: var(--text-muted);">
                                <span><i class="fas fa-layer-group"></i> {{ $series->sections->count() }} Sections</span>
                                <span><i class="fas fa-star text-warning"></i> {{ $series->total_marks }} Marks</span>
                            </div>
                        </div>
                        <a href="{{ route('student.view-sections', $series->id) }}" class="btn btn-primary btn-block">View Details & Attempt</a>
                    </div>
                @endforeach
            </div>
        @elseif(!$isSubscribed && $demoSeries->count() > 0)
            <div class="grid-2 animate-in delay-1">
                @foreach($demoSeries as $series)
                    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-color: var(--success);">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                                <div style="font-size: 2.5rem; color: var(--success);"><i class="fas fa-vial"></i></div>
                                <div class="badge badge-success">Free Demo</div>
                            </div>
                            <h3 style="font-size: 1.2rem; margin-bottom: 8px;">{{ $series->title }}</h3>
                            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 20px;">{{ Str::limit($series->description, 100) }}</p>
                            <div style="display: flex; gap: 16px; margin-bottom: 24px; font-size: 0.85rem; color: var(--text-muted);">
                                <span><i class="fas fa-layer-group"></i> {{ $series->sections->count() }} Sections</span>
                                <span><i class="fas fa-star text-warning"></i> {{ $series->total_marks }} Marks</span>
                            </div>
                        </div>
                        <a href="{{ route('student.view-sections', $series->id) }}" class="btn btn-success btn-block">Try Demo Test</a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state card animate-in delay-1">
                <i class="fas fa-folder-open"></i>
                <h3>No Test Series Available</h3>
                <p>The teacher hasn't uploaded any tests for this course yet.</p>
            </div>
        @endif
    </main>
</div>
@endsection
