@extends('layouts.app')

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 40px;
        margin-top: 30px;
    }
    .timeline::before {
        content: ''; position: absolute; top: 0; left: 15px;
        height: 100%; width: 2px; background: var(--dark-border);
    }
    .timeline-item { position: relative; margin-bottom: 30px; }
    .timeline-marker {
        position: absolute; left: -40px; top: 0;
        width: 32px; height: 32px; border-radius: 50%;
        background: var(--dark-card); border: 2px solid var(--dark-border);
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; z-index: 2; transition: all 0.3s ease;
    }
    .timeline-item.unlocked .timeline-marker { border-color: var(--primary); background: rgba(99,102,241,0.1); color: var(--primary); }
    .timeline-item.completed .timeline-marker { border-color: var(--success); background: var(--success); color: white; }
    
    .section-card {
        background: var(--dark-card); border: 1px solid var(--dark-border);
        border-radius: 12px; padding: 20px; transition: all 0.3s ease;
    }
    .timeline-item.unlocked .section-card { border-color: var(--primary); }
    .timeline-item.locked .section-card { opacity: 0.6; }
    
</style>
@endpush

@section('content')
<div class="container animate-in" style="max-width: 900px; padding-top: 40px;">
    <a href="javascript:history.back()" style="color: var(--text-secondary); text-decoration: none; margin-bottom: 20px; display: inline-block;"><i class="fas fa-arrow-left"></i> Back</a>
    
    <div class="card mb-5" style="background: linear-gradient(to right, var(--dark-card), rgba(99,102,241,0.05)); border-left: 4px solid var(--primary);">
        <h1 style="font-size: 1.8rem; margin-bottom: 8px;">{{ $testSeries->title }} {!! $testSeries->is_demo ? '<span class="badge badge-success" style="vertical-align: middle;">Demo</span>' : '' !!}</h1>
        <p style="color: var(--text-secondary); margin-bottom: 16px;">{{ $testSeries->description }}</p>
        <div style="display: flex; gap: 20px; font-size: 0.9rem; font-weight: 600;">
            <span class="text-primary"><i class="fas fa-layer-group"></i> {{ $testSeries->sections->count() }} Sections Sequence</span>
            <span class="text-warning"><i class="fas fa-star"></i> {{ $testSeries->total_marks }} Total Marks</span>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <strong>Important:</strong> You must complete sections in sequential order. A section unlocks only after you complete the previous one.
    </div>

    <div class="timeline">
        @foreach($sectionsData as $index => $data)
            @php
                $section = $data['section'];
                $isUnlocked = $data['is_unlocked'];
                $status = $data['status'];
                
                $itemClass = 'locked';
                if ($status === 'completed') $itemClass = 'completed';
                elseif ($isUnlocked) $itemClass = 'unlocked';
            @endphp
            
            <div class="timeline-item {{ $itemClass }}">
                <div class="timeline-marker">
                    @if($status === 'completed')
                        <i class="fas fa-check"></i>
                    @elseif($isUnlocked)
                        {{ $index + 1 }}
                    @else
                        <i class="fas fa-lock" style="font-size: 0.8rem;"></i>
                    @endif
                </div>
                
                <div class="section-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
                        <div>
                            <h3 style="font-size: 1.2rem; margin-bottom: 8px;">Section {{ $index + 1 }}: {{ $section->title }}</h3>
                            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 12px;">{{ $section->description }}</p>
                            <div style="display: flex; gap: 16px; font-size: 0.8rem; color: var(--text-muted);">
                                <span><i class="fas fa-clock"></i> {{ $section->duration_minutes }} Mins</span>
                                <span><i class="fas fa-question-circle"></i> {{ $section->questions->count() }} Questions</span>
                                <span><i class="fas fa-bullseye"></i> {{ $section->total_marks }} Marks</span>
                                <span><i class="fas fa-flag-checkered"></i> Pass: {{ $section->passing_marks }}</span>
                            </div>
                        </div>
                        
                        <div style="text-align: right; min-width: 150px;">
                            @if($status === 'completed')
                                <div style="margin-bottom: 8px;">
                                    <div style="font-size: 1.5rem; font-weight: 800; color: {{ $data['attempt']->percentage >= $section->passing_marks ? 'var(--success)' : 'var(--danger)' }};">{{ $data['attempt']->percentage }}%</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">Score</div>
                                </div>
                                <a href="{{ route('student.test-report', $data['attempt']->id) }}" class="btn btn-secondary btn-sm"><i class="fas fa-chart-bar"></i> View Report</a>
                            @elseif($status === 'in_progress')
                                <div class="badge badge-warning mb-2" style="display: block; width: fit-content; margin-left: auto;">In Progress</div>
                                <a href="{{ route('student.start-test', $section->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-play"></i> Resume</a>
                            @elseif($isUnlocked)
                                <a href="{{ route('student.start-test', $section->id) }}" class="btn btn-primary"><i class="fas fa-play"></i> Start Section</a>
                            @else
                                <button class="btn btn-secondary" disabled style="opacity: 0.5; cursor: not-allowed;"><i class="fas fa-lock"></i> Locked</button>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 6px;">Complete Section {{ $index }} to unlock</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
