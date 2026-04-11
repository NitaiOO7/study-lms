@extends('layouts.app')

@push('styles')
<style>
    .universal-banner {
        background: url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover;
        position: relative;
        padding: 40px;
        border-radius: 16px;
        margin-bottom: 30px;
        overflow: hidden;
    }
    .universal-banner::before {
        content: ''; position: absolute; top:0; left:0; width:100%; height:100%;
        background: linear-gradient(to right, rgba(15,15,35,0.9), rgba(99,102,241,0.6));
    }
    .universal-banner-content { position: relative; z-index: 1; }
</style>
@endpush

@section('content')
<div class="container animate-in">
    <div class="page-header mt-4">
        <div>
            <h1 class="page-title">Community Forums</h1>
            <p class="page-subtitle">Connect, discuss, and learn together.</p>
        </div>
    </div>

    <!-- Universal Group -->
    @if($universalGroup)
        <a href="{{ route('community.group', $universalGroup->slug) }}" style="text-decoration: none;">
            <div class="universal-banner card">
                <div class="universal-banner-content">
                    <div class="badge badge-primary mb-2"><i class="fas fa-globe"></i> Global</div>
                    <h2 style="font-size: 2rem; font-weight: 800; color: white; margin-bottom: 8px;">{{ $universalGroup->name }}</h2>
                    <p style="color: rgba(255,255,255,0.8); max-width: 600px; line-height: 1.5; margin-bottom: 20px;">
                        {{ $universalGroup->description }}
                    </p>
                    <div class="btn btn-primary">Join the Global Discussion <i class="fas fa-arrow-right"></i></div>
                </div>
            </div>
        </a>
    @endif

    <h2 class="section-title mt-5 mb-4">Subject Specific Forums</h2>
    
    @if($subjectGroups->count() > 0)
        <!-- Group by Level -->
        @php
            $groupedForums = $subjectGroups->groupBy(function($item) {
                return $item->subject ? $item->subject->level : 'other';
            });
        @endphp

        @foreach($groupedForums as $level => $forums)
            <h3 style="font-size: 1.1rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px; margin-top: 32px;">{{ $level }} Level</h3>
            <div class="grid-4">
                @foreach($forums as $group)
                    <a href="{{ route('community.group', $group->slug) }}" style="text-decoration: none;">
                        <div class="card" style="display: flex; align-items: center; gap: 16px; padding: 20px;">
                            <div class="avatar" style="width: 50px; height: 50px; font-size: 1.5rem; background: rgba(255,255,255,0.05); border: 1px solid var(--dark-border);">
                                {{ $group->icon ?? '📚' }}
                            </div>
                            <div>
                                <h4 style="color: var(--text-primary); font-weight: 600; margin-bottom: 4px;">{{ $group->subject->name }}</h4>
                                <div style="font-size: 0.8rem; color: var(--text-muted);"><i class="fas fa-comments"></i> Enter Forum</div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endforeach
    @else
        <div class="empty-state card">
            <i class="fas fa-users-slash"></i>
            <h3>No subject forums available yet.</h3>
        </div>
    @endif
</div>
@endsection
