@extends('layouts.app')

@section('content')
<div class="container py-5 animate-in">
    <div class="page-header mb-5">
        <div>
            <h1 class="page-title">Community Forums</h1>
            <p class="page-subtitle">Connect, share, and grow with {{ auth()->user()->hasRole('teacher') ? 'fellow educators' : 'other students' }}.</p>
        </div>
    </div>

    <div class="community-sections">
        @php
            $typeLabels = [
                'universal' => ['title' => 'Global Discussion Hub', 'desc' => 'Open to everyone - Students & Teachers', 'color' => 'var(--primary)'],
                'student' => ['title' => 'Students Sanctuary', 'desc' => 'Exclusive zone for Students only', 'color' => '#10b981'],
                'teacher' => ['title' => 'Teachers Lounge', 'desc' => 'Private collaboration for Educators', 'color' => '#f59e0b']
            ];
            
            // Define order
            $orderedTypes = ['universal', 'student', 'teacher'];
        @endphp

        @foreach($orderedTypes as $type)
            @if(isset($groups[$type]))
                <div class="mb-5 section-group animate-in" style="--delay: {{ $loop->index * 0.2 }}s">
                    <div class="d-flex align-items-center mb-4">
                        <div class="section-indicator me-3" style="background: {{ $typeLabels[$type]['color'] }}"></div>
                        <div>
                            <h2 class="section-title m-0">{{ $typeLabels[$type]['title'] }}</h2>
                            <p class="text-muted small m-0">{{ $typeLabels[$type]['desc'] }}</p>
                        </div>
                    </div>
                    
                    <div class="grid-3">
                        @foreach($groups[$type] as $group)
                        <div class="card p-4 forum-group-card">
                            <div class="d-flex align-items-center mb-3">
                                <div class="forum-icon me-3">{{ $group->icon ?? '💬' }}</div>
                                <div class="overflow-hidden">
                                    <h3 class="m-0 h5 text-truncate"><a href="{{ route('community.group', $group->slug) }}">{{ $group->name }}</a></h3>
                                    <small class="text-muted">{{ $group->subject ? $group->subject->name : 'General' }}</small>
                                </div>
                            </div>
                            <p class="text-muted small mb-4 flex-grow-1">{{ Str::limit($group->description, 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                <div class="small text-muted"><i class="fas fa-users me-1"></i> {{ rand(50, 1000) }} members</div>
                                <a href="{{ route('community.group', $group->slug) }}" class="btn btn-primary btn-sm px-3 rounded-pill">Explore</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    @if($groups->isEmpty())
    <div class="card text-center p-5">
        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
        <h3>No communities available yet</h3>
        <p class="text-muted">Stay tuned as we launch new discussion groups for your subjects.</p>
    </div>
    @endif
</div>

<style>
    .section-indicator {
        width: 6px;
        height: 40px;
        border-radius: 10px;
    }
    .forum-group-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 1px solid rgba(255,255,255,0.05);
        background: rgba(30, 41, 59, 0.5);
        backdrop-filter: blur(10px);
    }
    .forum-group-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        border-color: var(--primary);
    }
    .forum-icon {
        width: 45px;
        height: 45px;
        background: rgba(var(--primary-rgb), 0.1);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .animate-in {
        animation: fadeIn 0.5s ease forwards;
        animation-delay: var(--delay);
        opacity: 0;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
