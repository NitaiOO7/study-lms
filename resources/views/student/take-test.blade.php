@extends('layouts.app')

@push('styles')
<style>
    .test-header {
        position: sticky;
        top: 70px;
        background: var(--dark-surface);
        padding: 16px 0;
        border-bottom: 1px solid var(--dark-border);
        z-index: 100;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .timer-badge {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: var(--danger);
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 700;
        font-family: monospace;
        font-size: 1.2rem;
        display: flex; align-items: center; gap: 8px;
    }
    .timer-badge.safe {
        background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.3); color: var(--success);
    }
    .question-card {
        background: var(--dark-card);
        border: 1px solid var(--dark-border);
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 24px;
        scroll-margin-top: 150px;
    }
    .option-label {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 16px; background: rgba(255,255,255,0.03);
        border: 1px solid var(--dark-border); border-radius: 8px;
        cursor: pointer; transition: all 0.2s ease; margin-bottom: 12px;
    }
    .option-label:hover { background: rgba(99,102,241,0.05); border-color: rgba(99,102,241,0.3); }
    .option-input:checked + .option-label {
        background: rgba(99,102,241,0.1); border-color: var(--primary);
    }
    .option-input { display: none; }
    .option-custom-radio {
        width: 20px; height: 20px; border-radius: 50%; border: 2px solid var(--text-muted);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;
    }
    .option-input:checked + .option-label .option-custom-radio { border-color: var(--primary); }
    .option-input:checked + .option-label .option-custom-radio::after {
        content: ''; width: 10px; height: 10px; border-radius: 50%; background: var(--primary);
    }
</style>
@endpush

@section('content')
<!-- Sticky Header -->
<div class="test-header">
    <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 1.2rem; margin-bottom: 4px;">{{ $section->title }}</h2>
            <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $section->questions->count() }} Questions • {{ $section->total_marks }} Marks</div>
        </div>
        <div class="timer-badge safe" id="timer">
            <i class="fas fa-stopwatch"></i> <span id="time-display">--:--</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top: 30px; padding-bottom: 60px; max-width: 800px;">
    <form action="{{ route('student.submit-test', $attempt->id) }}" method="POST" id="test-form">
        @csrf
        
        @foreach($questions as $index => $question)
            <div class="question-card" id="q-{{ $question->id }}">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--dark-border); padding-bottom: 16px; margin-bottom: 20px;">
                    <h3 style="font-size: 1.1rem;">Question {{ $index + 1 }}</h3>
                    <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">
                        <span class="text-success">+{{ $question->marks }}</span> / <span class="text-danger">-{{ $question->negative_marks }}</span>
                    </div>
                </div>
                
                <div style="font-size: 1.05rem; line-height: 1.6; margin-bottom: 24px;">
                    {!! nl2br(e($question->question_text)) !!}
                </div>
                
                @if($question->question_image)
                    <div style="margin-bottom: 24px; border-radius: 8px; overflow: hidden; border: 1px solid var(--dark-border);">
                        <img src="{{ Storage::url($question->question_image) }}" alt="Question Image" style="max-width: 100%;">
                    </div>
                @endif
                
                <div class="options-container">
                    @foreach($question->options as $option)
                        <div style="position: relative;">
                            <input type="radio" name="question_{{ $question->id }}" id="opt_{{ $option->id }}" value="{{ $option->id }}" class="option-input">
                            <label for="opt_{{ $option->id }}" class="option-label">
                                <div class="option-custom-radio"></div>
                                <div style="flex: 1;">
                                    {{ $option->option_text }}
                                    @if($option->option_image)
                                        <div style="margin-top: 10px; border-radius: 4px; overflow: hidden;">
                                            <img src="{{ Storage::url($option->option_image) }}" style="max-height: 150px;">
                                        </div>
                                    @endif
                                </div>
                            </label>
                        </div>
                    @endforeach
                    <div style="text-align: right; margin-top: 10px;">
                        <button type="button" class="btn btn-sm" style="background:transparent; color: var(--text-muted); border: 1px solid var(--dark-border);" onclick="clearRadio('question_{{ $question->id }}')">Clear Selection</button>
                    </div>
                </div>
            </div>
        @endforeach
        
        <div class="card text-center" style="padding: 40px;">
            <i class="fas fa-flag-checkered" style="font-size: 3rem; color: var(--primary); margin-bottom: 16px;"></i>
            <h3 style="margin-bottom: 16px;">Ready to Submit?</h3>
            <p style="color: var(--text-secondary); margin-bottom: 24px;">Please review your answers before submitting. You cannot change them once submitted.</p>
            <button type="button" id="submit-btn" class="btn btn-primary btn-lg" style="min-width: 200px;" onclick="confirmSubmit()">Submit Section</button>
        </div>
    </form>
</div>

<!-- Submit Confirmation Modal -->
<div class="modal-overlay" id="confirm-modal">
    <div class="modal" style="max-width: 400px; text-align: center;">
        <i class="fas fa-exclamation-circle text-warning" style="font-size: 4rem; margin-bottom: 20px;"></i>
        <h3 class="modal-title">Confirm Submission</h3>
        <p style="color: var(--text-secondary); margin-bottom: 30px;">Are you sure you want to submit this section? This action cannot be undone.</p>
        <div style="display: flex; gap: 16px; justify-content: center;">
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('confirm-modal').classList.remove('active')">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="document.getElementById('test-form').submit()">Yes, Submit</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function clearRadio(name) {
        let radios = document.getElementsByName(name);
        for(let i=0; i<radios.length; i++) radios[i].checked = false;
    }
    
    function confirmSubmit() {
        document.getElementById('confirm-modal').classList.add('active');
    }

    // Timer Logic
    const durationMinutes = {{ $section->duration_minutes }};
    const startTimeStamp = new Date('{{ $attempt->started_at->toIso8601String() }}').getTime();
    const durationMs = durationMinutes * 60 * 1000;
    const endTimeStamp = startTimeStamp + durationMs;

    const timerBadge = document.getElementById('timer');
    const timeDisplay = document.getElementById('time-display');

    function updateTimer() {
        const now = new Date().getTime();
        const distance = endTimeStamp - now;

        if (distance < 0) {
            clearInterval(timerInterval);
            timeDisplay.innerHTML = "00:00";
            timerBadge.classList.remove('safe');
            timerBadge.style.color = "white";
            timerBadge.style.background = "var(--danger)";
            alert("Time is up! Submitting your test automatically.");
            document.getElementById('test-form').submit();
            return;
        }

        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        timeDisplay.innerHTML = (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;

        // Change color when < 5 mins left
        if(minutes < 5 && timerBadge.classList.contains('safe')) {
            timerBadge.classList.remove('safe');
        }
    }

    const timerInterval = setInterval(updateTimer, 1000);
    updateTimer(); // Initial call
</script>
@endpush
