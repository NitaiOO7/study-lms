@extends('layouts.app')

@push('styles')
<style>
    .report-card { background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 16px; padding: 30px; margin-bottom: 24px; text-align: center; }
    .score-circle { width: 120px; height: 120px; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto 20px; border: 8px solid var(--dark-surface); }
    .pass-circle { border-color: var(--success); color: var(--success); }
    .fail-circle { border-color: var(--danger); color: var(--danger); }
    .score-big { font-size: 2.2rem; font-weight: 900; line-height: 1; margin-bottom: 4px; }
    
    .answer-card { background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; padding: 24px; margin-bottom: 20px; }
    .res-correct { border-left: 4px solid var(--success); }
    .res-wrong { border-left: 4px solid var(--danger); }
    .res-skipped { border-left: 4px solid var(--text-muted); }
    
    .opt-correct { background: rgba(16,185,129,0.1); border: 1px solid var(--success); border-radius: 6px; padding: 10px; margin-bottom: 8px; }
    .opt-wrong { background: rgba(239,68,68,0.1); border: 1px solid var(--danger); border-radius: 6px; padding: 10px; margin-bottom: 8px; }
    .opt-normal { background: rgba(255,255,255,0.03); border: 1px solid transparent; border-radius: 6px; padding: 10px; margin-bottom: 8px; }
</style>
@endpush

@section('content')
@php
    $isPass = $attempt->percentage >= $attempt->section->passing_marks;
@endphp
<div class="container animate-in" style="padding-top: 40px; padding-bottom: 60px;">
    
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
        <div>
            <h1 class="page-title">Test Report</h1>
            <p class="page-subtitle">{{ $attempt->testSeries->title }} - {{ $attempt->section->title }}</p>
        </div>
        <a href="{{ route('student.view-sections', $attempt->testSeries->id) }}" class="btn btn-primary">Back to Sections</a>
    </div>

    <!-- Summary Overview -->
    <div class="grid-3 mb-4">
        <!-- Score Summary -->
        <div class="report-card" style="display: flex; flex-direction: column; justify-content: center;">
            <div class="score-circle {{ $isPass ? 'pass-circle' : 'fail-circle' }}">
                <div class="score-big">{{ $attempt->percentage }}%</div>
            </div>
            <h3 style="margin-bottom: 8px;">{{ $isPass ? 'Section Passed!' : 'Section Failed' }}</h3>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">Score: {{ $attempt->score }} / {{ $attempt->total_marks }} Marks</p>
            <p style="color: var(--text-muted); font-size: 0.8rem; margin-top: 8px;">Required: {{ $attempt->section->passing_marks }}%</p>
        </div>
        
        <!-- Accuracy Details -->
        <div class="report-card">
            <h3 style="margin-bottom: 24px; font-size: 1.1rem; text-align: left;">Performance Breakdown</h3>
            <div style="display: flex; justify-content: space-between; margin-bottom: 16px; align-items: center;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 12px; height: 12px; border-radius: 50%; background: var(--success);"></div>
                    <span style="color: var(--text-secondary);">Correct</span>
                </div>
                <div style="font-weight: 700;">{{ $attempt->correct }}</div>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 16px; align-items: center;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 12px; height: 12px; border-radius: 50%; background: var(--danger);"></div>
                    <span style="color: var(--text-secondary);">Incorrect</span>
                </div>
                <div style="font-weight: 700;">{{ $attempt->wrong }}</div>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 16px; align-items: center;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 12px; height: 12px; border-radius: 50%; background: var(--text-muted);"></div>
                    <span style="color: var(--text-secondary);">Skipped/Unanswered</span>
                </div>
                <div style="font-weight: 700;">{{ $attempt->skipped }}</div>
            </div>
            <div style="width: 100%; height: 6px; border-radius: 3px; display: flex; overflow: hidden; margin-top: 24px; background: var(--dark-surface);">
                @if($attempt->total_questions > 0)
                    <div style="height: 100%; width: {{ ($attempt->correct/$attempt->total_questions)*100 }}%; background: var(--success);"></div>
                    <div style="height: 100%; width: {{ ($attempt->wrong/$attempt->total_questions)*100 }}%; background: var(--danger);"></div>
                @endif
            </div>
        </div>

        <!-- Rank & Leaderboard Summary -->
        <div class="report-card">
            <h3 style="margin-bottom: 24px; font-size: 1.1rem; text-align: left;">Rank Comparison</h3>
            <div style="font-size: 3rem; font-weight: 800; color: var(--primary); margin-bottom: 8px;">#{{ $rank }}</div>
            <div style="color: var(--text-secondary); margin-bottom: 20px;">Out of {{ $totalAttempts }} learners</div>
            
            <div style="background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.2); padding: 12px; border-radius: 8px;">
                <div style="font-weight: 700; color: var(--primary-light);">{{ $percentile }} Percentile</div>
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">You scored better than {{ $percentile }}% of students.</div>
            </div>
        </div>
    </div>

    <!-- Detailed Solutions -->
    <h2 class="section-title mt-5 mb-4">Detailed Solutions</h2>
    @foreach($attempt->answers as $index => $answer)
        @php
            $q = $answer->question;
            $statusClass = $answer->is_correct ? 'res-correct' : ($answer->selected_option_id ? 'res-wrong' : 'res-skipped');
        @endphp
        <div class="answer-card {{ $statusClass }}">
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                <div style="font-weight: 600;">Question {{ $index + 1 }}</div>
                <div>
                    @if($answer->is_correct) <span class="badge badge-success">Correct (+{{ $q->marks }})</span>
                    @elseif($answer->selected_option_id) <span class="badge badge-danger">Incorrect (-{{ $q->negative_marks }})</span>
                    @else <span class="badge" style="background: var(--dark-surface);">Skipped (0)</span>
                    @endif
                </div>
            </div>
            
            <div style="font-size: 1.05rem; margin-bottom: 20px;">{!! nl2br(e($q->question_text)) !!}</div>
            @if($q->question_image)
                <img src="{{ Storage::url($q->question_image) }}" style="max-height: 200px; margin-bottom: 20px; border-radius: 8px;">
            @endif

            <div style="margin-bottom: 20px;">
                @foreach($q->options as $option)
                    @php
                        $optClass = 'opt-normal';
                        if($option->is_correct) $optClass = 'opt-correct';
                        elseif($answer->selected_option_id == $option->id && !$option->is_correct) $optClass = 'opt-wrong';
                    @endphp
                    <div class="{{ $optClass }}">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>{{ $option->option_text }}</div>
                            @if($option->is_correct) <i class="fas fa-check text-success"></i>
                            @elseif($answer->selected_option_id == $option->id) <i class="fas fa-times text-danger"></i>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($q->explanation)
                <div style="background: var(--dark-surface); padding: 16px; border-radius: 8px; border-left: 3px solid var(--info);">
                    <div style="font-weight: 600; color: var(--info); margin-bottom: 8px;"><i class="fas fa-lightbulb"></i> Explanation</div>
                    <div style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.5;">{!! nl2br(e($q->explanation)) !!}</div>
                </div>
            @endif
        </div>
    @endforeach
</div>
@endsection
