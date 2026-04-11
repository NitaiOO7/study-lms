@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-section">
            <div class="sidebar-title">Menu</div>
            <a href="{{ route('teacher.dashboard') }}" class="sidebar-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="{{ route('teacher.courses') }}" class="sidebar-link"><i class="fas fa-book"></i> My Courses</a>
            <a href="{{ route('teacher.materials') }}" class="sidebar-link active"><i class="fas fa-file-alt"></i> Study Materials</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header animate-in">
            <div>
                <h1 class="page-title">Study Materials</h1>
                <p class="page-subtitle">Upload and manage PDFs, Document, and Video Links.</p>
            </div>
            <button class="btn btn-primary" onclick="document.getElementById('upload-modal').classList.add('active')"><i class="fas fa-upload"></i> Upload Material</button>
        </div>

        @if($materials->count() > 0)
            <div class="grid-3 animate-in delay-1">
                @foreach($materials as $material)
                    <div class="card" style="display: flex; gap: 16px; align-items: flex-start; padding: 20px;">
                        <div style="font-size: 2.5rem; color: var(--primary);">
                            @if($material->type == 'pdf') <i class="fas fa-file-pdf text-danger"></i>
                            @elseif($material->type == 'video') <i class="fas fa-file-video text-info"></i>
                            @elseif($material->type == 'link') <i class="fas fa-link text-primary"></i>
                            @else <i class="fas fa-file-alt text-warning"></i>
                            @endif
                        </div>
                        <div style="flex: 1;">
                            <h3 style="font-size: 1.1rem; margin-bottom: 4px;">{{ $material->title }}</h3>
                            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 8px;">{{ $material->subject->name }}</div>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                @if($material->is_free) <span class="badge badge-success">Free</span> @endif
                                @if($material->file_size) <span class="badge badge-info">{{ $material->file_size }} KB</span> @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $materials->links() }}</div>
        @else
            <div class="empty-state card animate-in delay-1">
                <i class="fas fa-file-upload"></i>
                <h3>No Materials Uploaded</h3>
                <p>Provide supplementary materials like PDFs and videos for your students.</p>
            </div>
        @endif
    </main>
</div>

<!-- Upload Modal -->
<div class="modal-overlay" id="upload-modal">
    <div class="modal">
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <h3 class="modal-title m-0">Upload Study Material</h3>
            <button class="btn" style="background: none; padding: 0;" onclick="document.getElementById('upload-modal').classList.remove('active')"><i class="fas fa-times text-muted"></i></button>
        </div>
        
        <form action="{{ route('teacher.materials.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-input" required>
            </div>
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Subject <span class="text-danger">*</span></label>
                    <select name="subject_id" class="form-select" required>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Course (Optional)</label>
                    <select name="course_id" class="form-select">
                        <option value="">Select Course...</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select" required id="material-type">
                    <option value="pdf">PDF Document</option>
                    <option value="document">Other Document (Word/Excel)</option>
                    <option value="image">Image</option>
                    <option value="video">Video Link</option>
                    <option value="link">External Link</option>
                </select>
            </div>
            
            <div class="form-group" id="file-input-group">
                <label class="form-label">Upload File</label>
                <input type="file" name="file" class="form-input">
            </div>
            
            <div class="form-group" id="url-input-group" style="display: none;">
                <label class="form-label">External URL</label>
                <input type="url" name="external_url" class="form-input" placeholder="https://youtube.com/...">
            </div>
            
            <div class="checkbox-wrap mt-2 mb-4">
                <input type="checkbox" name="is_free" value="1">
                <label>Make this material accessible to everyone for free?</label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Upload</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('material-type').addEventListener('change', function() {
        let val = this.value;
        if(val === 'video' || val === 'link') {
            document.getElementById('file-input-group').style.display = 'none';
            document.getElementById('url-input-group').style.display = 'block';
        } else {
            document.getElementById('file-input-group').style.display = 'block';
            document.getElementById('url-input-group').style.display = 'none';
        }
    });
</script>
@endsection
