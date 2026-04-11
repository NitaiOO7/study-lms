@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-section">
            <div class="sidebar-title">Menu</div>
            <a href="{{ route('student.dashboard') }}" class="sidebar-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="{{ route('student.browse') }}" class="sidebar-link"><i class="fas fa-compass"></i> Browse Courses</a>
            <a href="{{ route('student.my-courses') }}" class="sidebar-link active"><i class="fas fa-book-reader"></i> My Learning</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header animate-in">
            <div>
                <h1 class="page-title">My Learning</h1>
                <p class="page-subtitle">Pick up where you left off.</p>
            </div>
        </div>

        @if($subscriptions->count() > 0)
            <div class="grid-3 animate-in delay-1">
                @foreach($subscriptions as $sub)
                    <div class="course-card">
                        <div class="course-thumb">
                            @if($sub->course->thumbnail)
                                <img src="{{ Storage::url($sub->course->thumbnail) }}" alt="{{ $sub->course->title }}">
                            @else
                                <i class="fas fa-book-open"></i>
                            @endif
                        </div>
                        <div class="course-body">
                            <div class="course-meta">
                                <span><i class="fas fa-tv text-primary"></i> {{ $sub->course->channel->name }}</span>
                            </div>
                            <h3 class="course-title"><a href="{{ route('student.course.detail', $sub->course->slug) }}">{{ Str::limit($sub->course->title, 50) }}</a></h3>
                            
                            <div style="margin: 16px 0;">
                                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 4px; color: var(--text-secondary);">
                                    <span>Access Expires:</span>
                                    <span>{{ $sub->expires_at ? $sub->expires_at->format('M d, Y') : 'Lifetime' }}</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 50%;"></div>
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('student.materials', $sub->course->slug) }}" class="btn btn-secondary btn-sm" style="flex:1;"><i class="fas fa-file-pdf"></i> Materials</a>
                                <a href="{{ route('student.test-series', $sub->course->slug) }}" class="btn btn-primary btn-sm" style="flex:1;"><i class="fas fa-tasks"></i> Tests</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-4 animate-in delay-2">
                {{ $subscriptions->links() }}
            </div>
        @else
            <div class="empty-state card animate-in delay-1">
                <i class="fas fa-graduation-cap"></i>
                <h3>No courses enrolled</h3>
                <p>You haven't enrolled in any courses yet.</p>
                <a href="{{ route('student.browse') }}" class="btn btn-primary mt-3">Browse Premium Courses</a>
            </div>
        @endif
    </main>
</div>
@endsection
