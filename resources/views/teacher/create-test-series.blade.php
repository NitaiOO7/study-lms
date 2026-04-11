@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <!-- Sidebar content same as others -->
        <div class="sidebar-section">
            <div class="sidebar-title">Menu</div>
            <a href="{{ route('teacher.dashboard') }}" class="sidebar-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="{{ route('teacher.test-series') }}" class="sidebar-link active"><i class="fas fa-tasks"></i> Test Series</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header animate-in">
            <div>
                <a href="{{ route('teacher.test-series') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;"><i class="fas fa-arrow-left"></i> Back</a>
                <h1 class="page-title mt-2">Create Test Series</h1>
            </div>
        </div>

        <div class="card animate-in delay-1" style="max-width: 800px;">
            <form action="{{ route('teacher.test-series.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Test Series Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Associated Course <span class="text-danger">*</span></label>
                    <select name="course_id" class="form-select" required>
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea"></textarea>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Total Marks <span class="text-danger">*</span></label>
                        <input type="number" name="total_marks" min="1" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Passing Marks <span class="text-danger">*</span></label>
                        <input type="number" name="passing_marks" min="0" class="form-input" required>
                    </div>
                </div>

                <div class="checkbox-wrap mt-2">
                    <input type="checkbox" name="is_demo" id="is_demo" value="1">
                    <label for="is_demo">Make this a Free Demo Test? (Accessible without subscription)</label>
                </div>
                
                <div class="checkbox-wrap mb-4">
                    <input type="checkbox" name="is_published" id="is_published" value="1">
                    <label for="is_published">Publish immediately?</label>
                </div>

                <div style="border-top: 1px solid var(--dark-border); padding-top: 20px;">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save & Continue to Sections</button>
                    <a href="{{ route('teacher.test-series') }}" class="btn btn-secondary ml-2">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection
