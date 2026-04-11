@extends('layouts.app')

@section('content')
<div class="container animate-in" style="padding-top: 40px; padding-bottom: 60px;">
    
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
        <div>
            <a href="{{ route('student.course.detail', $course->slug) }}" style="color: var(--text-secondary); text-decoration: none; margin-bottom: 10px; display: inline-block;">&larr; Back to Course</a>
            <h1 class="page-title">Study Materials</h1>
            <p class="page-subtitle">{{ $course->title }}</p>
        </div>
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
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 16px;">{{ $material->description }}</div>
                        
                        @if($material->file_path)
                            <a href="{{ Storage::url($material->file_path) }}" target="_blank" class="btn btn-secondary btn-sm btn-block"><i class="fas fa-download"></i> Download</a>
                        @elseif($material->external_url)
                            <a href="{{ $material->external_url }}" target="_blank" class="btn btn-secondary btn-sm btn-block"><i class="fas fa-external-link-alt"></i> Open Link</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $materials->links() }}</div>
    @else
        <div class="empty-state card animate-in delay-1">
            <i class="fas fa-folder-open"></i>
            <h3>No Materials Available</h3>
            <p>The teacher hasn't uploaded any supplementary materials for this course yet.</p>
        </div>
    @endif
</div>
@endsection
