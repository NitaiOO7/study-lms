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
                <a href="{{ route('teacher.lessons', $course->id) }}" style="color: var(--text-secondary); text-decoration: none; margin-bottom: 10px; display: inline-block;">&larr; Back to Lessons</a>
                <h1 class="page-title">Add New Lesson</h1>
                <p class="page-subtitle">{{ $course->title }}</p>
            </div>
        </div>

        <div class="card animate-in delay-1" style="max-width: 800px;">
            <form action="{{ route('teacher.lessons.store', $course->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">Lesson Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-input" rows="3"></textarea>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Video File (MP4/WebM)</label>
                        <input type="file" name="video_file" class="form-input" accept="video/mp4,video/webm">
                        <small class="text-muted">Max size: 100MB.</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Or External Video URL</label>
                        <input type="url" name="video_url" class="form-input" placeholder="https://youtube.com/...">
                    </div>
                </div>

                <hr style="border-color: var(--dark-border); margin: 24px 0;">

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Clean PDF File (Without Annotation)</label>
                        <input type="file" name="pdf_file" class="form-input" accept="application/pdf">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Annotated PDF File (With Annotation)</label>
                        <input type="file" name="annotated_pdf_file" class="form-input" accept="application/pdf">
                    </div>
                </div>
                
                <div class="checkbox-wrap mt-2 mb-4">
                    <input type="checkbox" name="is_free" value="1">
                    <label>Make this lesson accessible to everyone for free (Demo)</label>
                </div>
                
                <button type="submit" class="btn btn-primary">Save Lesson</button>
            </form>
        </div>
    </main>
</div>
@endsection
