@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-section">
            <div class="sidebar-title">Menu</div>
            <a href="{{ route('teacher.dashboard') }}" class="sidebar-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="{{ route('teacher.courses') }}" class="sidebar-link active"><i class="fas fa-book"></i> My Courses</a>
            <a href="{{ route('teacher.materials') }}" class="sidebar-link"><i class="fas fa-file-alt"></i> Study Materials</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header animate-in">
            <div>
                <a href="{{ route('teacher.courses') }}" style="color: var(--text-secondary); text-decoration: none; margin-bottom: 10px; display: inline-block;">&larr; Back to Courses</a>
                <h1 class="page-title">Manage Lessons</h1>
                <p class="page-subtitle">{{ $course->title }}</p>
            </div>
            <a href="{{ route('teacher.lessons.create', $course->id) }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Lesson</a>
        </div>

        @if($lessons->count() > 0)
            <div class="grid-3 animate-in delay-1">
                @foreach($lessons as $lesson)
                    <div class="card" style="display: flex; gap: 16px; align-items: flex-start; padding: 20px;">
                        <div style="font-size: 2.5rem; color: var(--primary);">
                            @if($lesson->video_path || $lesson->video_url) <i class="fas fa-video text-info"></i>
                            @else <i class="fas fa-file-alt text-warning"></i>
                            @endif
                        </div>
                        <div style="flex: 1;">
                            <h3 style="font-size: 1.1rem; margin-bottom: 4px;">{{ $lesson->title }}</h3>
                            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 12px;">Sort Order: {{ $lesson->sort_order }}</div>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                @if($lesson->pdf_path) <span class="badge badge-secondary" style="font-size: 0.7rem;">Clean PDF</span> @endif
                                @if($lesson->annotated_pdf_path) <span class="badge badge-success" style="font-size: 0.7rem;">Annotated PDF</span> @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $lessons->links() }}</div>
        @else
            <div class="empty-state card animate-in delay-1">
                <i class="fas fa-video"></i>
                <h3>No Lessons Found</h3>
                <p>Upload videos and PDFs to build out this course.</p>
                <a href="{{ route('teacher.lessons.create', $course->id) }}" class="btn btn-primary mt-3">Add Your First Lesson</a>
            </div>
        @endif
    </main>
</div>
@endsection
