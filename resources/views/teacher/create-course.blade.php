@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-section">
            <div class="sidebar-title">Menu</div>
            <a href="{{ route('teacher.dashboard') }}" class="sidebar-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="{{ route('teacher.courses') }}" class="sidebar-link active"><i class="fas fa-book"></i> My Courses</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header animate-in">
            <div>
                <a href="{{ route('teacher.courses') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;"><i class="fas fa-arrow-left"></i> Back to Courses</a>
                <h1 class="page-title mt-2">Create New Course</h1>
            </div>
        </div>

        <div class="card animate-in delay-1" style="max-width: 800px;">
            <form action="{{ route('teacher.courses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Course Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $level => $subjectGroup)
                                <optgroup label="{{ strtoupper($level) }}">
                                    @foreach($subjectGroup as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea"></textarea>
                </div>

                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">Price ($) <span class="text-danger">*</span></label>
                        <input type="number" name="price" step="0.01" min="0" value="0.00" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Duration (Days) <span class="text-danger">*</span></label>
                        <input type="number" name="duration_days" min="1" value="365" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Target Level <span class="text-danger">*</span></label>
                        <select name="level" class="form-select" required>
                            <option value="hs">High School</option>
                            <option value="graduate" selected>Graduate</option>
                            <option value="master">Master / PG</option>
                        </select>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label">Thumbnail Image</label>
                    <input type="file" name="thumbnail" class="form-input" accept="image/*">
                </div>
                
                <div class="checkbox-wrap mb-4">
                    <input type="checkbox" name="is_published" id="is_published" value="1">
                    <label for="is_published" style="color: var(--text-primary);">Publish immediately?</label>
                </div>

                <div style="border-top: 1px solid var(--dark-border); padding-top: 20px;">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save Course</button>
                    <a href="{{ route('teacher.courses') }}" class="btn btn-secondary ml-2">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection
