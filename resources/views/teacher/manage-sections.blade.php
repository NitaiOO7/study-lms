@extends('layouts.app')

@push('styles')
<style>
    .section-admin-card {
        background: var(--dark-card);
        border: 1px solid var(--dark-border);
        border-radius: 12px;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .section-admin-header {
        background: rgba(99,102,241,0.1);
        padding: 16px 24px;
        border-bottom: 1px solid var(--dark-border);
        display: flex; justify-content: space-between; align-items: center;
    }
    .question-row {
        padding: 16px 24px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        display: flex; justify-content: space-between; align-items: flex-start;
    }
    .question-row:last-child { border-bottom: none; }
</style>
@endpush

@section('content')
<div class="container animate-in" style="padding-top: 40px; padding-bottom: 60px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
        <div>
            <a href="{{ route('teacher.test-series') }}" style="color: var(--text-secondary); text-decoration: none; margin-bottom: 10px; display: inline-block;">&larr; Back to Test Series</a>
            <h1 class="page-title">Manage Sections: {{ $testSeries->title }}</h1>
        </div>
        <button class="btn btn-primary" onclick="document.getElementById('add-section-modal').classList.add('active')"><i class="fas fa-plus"></i> Add New Section</button>
    </div>

    @if($sections->count() > 0)
        @foreach($sections as $index => $section)
            <div class="section-admin-card">
                <div class="section-admin-header">
                    <div>
                        <div style="font-weight: 700; font-size: 1.1rem; margin-bottom: 4px;">Section {{ $index + 1 }}: {{ $section->title }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-secondary);">
                            {{ $section->duration_minutes }} Mins • {{ $section->questions->count() }} Questions • {{ $section->total_marks }} Marks • Passing: {{ $section->passing_marks }}
                        </div>
                    </div>
                    <button class="btn btn-secondary btn-sm" onclick="openQuestionModal({{ $section->id }})"><i class="fas fa-plus"></i> Add Question</button>
                </div>
                
                <div style="padding: 10px 0;">
                    @if($section->questions->count() > 0)
                        @foreach($section->questions as $qIndex => $question)
                            <div class="question-row">
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; margin-bottom: 8px;">Q{{ $qIndex + 1 }}. {{ Str::limit($question->question_text, 100) }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">
                                        [+{!! $question->marks !!} / -{!! $question->negative_marks !!}] • {{ $question->options->count() }} Options
                                    </div>
                                </div>
                                <button class="btn btn-sm" style="color: var(--danger); background: transparent; border: 1px solid var(--danger);"><i class="fas fa-trash"></i></button>
                            </div>
                        @endforeach
                    @else
                        <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
                            No questions added to this section yet.
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state card">
            <i class="fas fa-layer-group"></i>
            <h3>No sections exist</h3>
            <p>Add sections sequentially. Students must pass Section 1 before accessing Section 2.</p>
        </div>
    @endif
</div>

<!-- Add Section Modal -->
<div class="modal-overlay" id="add-section-modal">
    <div class="modal">
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <h3 class="modal-title m-0">Add New Section</h3>
            <button class="btn" style="background: none; padding: 0;" onclick="document.getElementById('add-section-modal').classList.remove('active')"><i class="fas fa-times text-muted"></i></button>
        </div>
        
        <form action="{{ route('teacher.sections.store', $testSeries->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Section Title</label>
                <input type="text" name="title" class="form-input" placeholder="e.g., Logical Reasoning Part 1" required>
            </div>
            
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Duration (Minutes)</label>
                    <input type="number" name="duration_minutes" min="1" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Total Marks</label>
                    <input type="number" name="total_marks" min="1" class="form-input" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Passing Marks (Required to proceed to next section)</label>
                <input type="number" name="passing_marks" min="0" class="form-input" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block mt-4">Save Section</button>
        </form>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal-overlay" id="add-question-modal">
    <div class="modal" style="max-width: 800px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <h3 class="modal-title m-0">Add Question</h3>
            <button class="btn" style="background: none; padding: 0;" onclick="document.getElementById('add-question-modal').classList.remove('active')"><i class="fas fa-times text-muted"></i></button>
        </div>
        
        <form id="question-form" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Question Text</label>
                    <textarea name="question_text" class="form-textarea" style="min-height: 80px;" required></textarea>
                </div>
                <div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Positive Marks</label>
                            <input type="number" name="marks" value="1" min="1" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Negative Marks</label>
                            <input type="number" name="negative_marks" step="0.25" value="0.25" min="0" class="form-input" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="mcq">Multiple Choice (MCQ)</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <h4 style="margin: 20px 0 10px; padding-bottom: 10px; border-bottom: 1px solid var(--dark-border);">Options</h4>
            
            <div class="grid-2" style="gap: 16px;">
                @for($i=0; $i<4; $i++)
                    <div class="card" style="padding: 16px; background: var(--dark-surface);">
                        <div style="display: flex; gap: 10px; align-items: flex-start;">
                            <input type="radio" name="correct_option" value="{{ $i }}" {{ $i==0 ? 'checked' : '' }} style="margin-top: 12px; transform: scale(1.5);">
                            <div style="flex: 1;">
                                <input type="text" name="options[{{$i}}][text]" class="form-input" placeholder="Option {{ $i+1 }} text" required style="margin-bottom: 8px; padding: 8px;">
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
            
            <div class="form-group mt-4">
                <label class="form-label">Explanation (Shown after test)</label>
                <textarea name="explanation" class="form-textarea" style="min-height: 60px;"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block mt-4">Save Question</button>
        </form>
    </div>
</div>

<script>
    function openQuestionModal(sectionId) {
        let form = document.getElementById('question-form');
        form.action = `/teacher/sections/${sectionId}/questions`;
        document.getElementById('add-question-modal').classList.add('active');
    }
</script>
@endsection
