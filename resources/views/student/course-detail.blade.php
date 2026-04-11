@extends('layouts.app')

@section('content')
<!-- Course Header -->
<div class="course-header" style="background: var(--dark-card); border-bottom: 1px solid var(--dark-border); padding: 40px 0; margin-top: -30px;">
    <div class="container animate-in">
        <div class="grid-2" style="align-items: center; gap: 40px;">
            <div>
                <div style="margin-bottom: 16px;">
                    <span class="badge badge-primary">{{ strtoupper($course->level) }}</span>
                    <span class="badge badge-info ml-2">{{ $course->subject->name }}</span>
                </div>
                <h1 class="page-title" style="font-size: 2.2rem; margin-bottom: 16px;">{{ $course->title }}</h1>
                <p style="color: var(--text-secondary); font-size: 1.1rem; line-height: 1.6; margin-bottom: 24px;">
                    {{ $course->description ?? 'No description provided.' }}
                </p>
                <div style="display: flex; align-items: center; gap: 24px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        @if($course->channel->logo)
                            <img src="{{ Storage::url($course->channel->logo) }}" alt="Logo" class="avatar">
                        @else
                            <div class="avatar"><i class="fas fa-tv"></i></div>
                        @endif
                        <div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">Created by</div>
                            <a href="{{ route('channel.profile', $course->channel->slug) }}" style="font-weight: 600; color: var(--text-primary); text-decoration: none;">{{ $course->channel->name }}</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="card card-glass" style="text-align: center; padding: 32px; box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
                    <div class="course-price {{ $course->is_free ? 'free' : '' }}" style="font-size: 2.5rem; margin-bottom: 8px;">
                        {{ $course->is_free ? 'Free' : '$' . $course->price }}
                    </div>
                    <div style="color: var(--text-secondary); margin-bottom: 24px; font-size: 0.9rem;">
                        <i class="fas fa-clock"></i> Access for {{ $course->duration_days }} Days
                    </div>
                    
                    @if($isSubscribed)
                        <div class="alert alert-success" style="justify-content: center;"><i class="fas fa-check-circle"></i> You are enrolled</div>
                        <a href="{{ route('student.test-series', $course->slug) }}" class="btn btn-primary btn-block btn-lg mt-3"><i class="fas fa-play"></i> Go to Tests</a>
                    @else
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-primary btn-block btn-lg"><i class="fas fa-sign-in-alt"></i> Login to Enroll</a>
                        @else
                            @if(auth()->user()->hasRole('student'))
                                <form action="{{ route('student.course.subscribe', $course->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-block btn-lg"><i class="fas fa-bolt"></i> Enroll Now</button>
                                </form>
                            @endif
                        @endguest
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container" style="padding-top: 40px; padding-bottom: 40px;">
    <div class="grid-2" style="grid-template-columns: 2fr 1fr;">
        <!-- Left Column -->
        <div class="animate-in delay-1">
            <h2 class="section-title">Course Content Overview</h2>
            
            <div class="card mt-4">
                <h3 style="font-size: 1.1rem; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-tasks text-accent"></i> Included Test Series
                </h3>
                @if($course->testSeries->count() > 0)
                    <div style="display: grid; gap: 12px;">
                        @foreach($course->testSeries as $series)
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: var(--dark-surface); border-radius: 10px; border: 1px solid var(--dark-border);">
                                <div>
                                    <div style="font-weight: 600;">{{ $series->title }} {!! $series->is_demo ? '<span class="badge badge-success">Demo</span>' : '' !!}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">{{ $series->sections->count() }} Sections • {{ $series->total_marks }} Marks</div>
                                </div>
                                @if($series->is_demo && !$isSubscribed)
                                    <a href="{{ route('student.view-sections', $series->id) }}" class="btn btn-secondary btn-sm">Try Demo</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No test series uploaded yet.</p>
                @endif
            </div>

            <div class="card mt-4">
                <h3 style="font-size: 1.1rem; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-file-video text-info"></i> Study Materials Included
                </h3>
                <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                    <span class="badge" style="background: rgba(255,255,255,0.05); border: 1px solid var(--dark-border); padding: 8px 16px;"><i class="fas fa-file-pdf text-danger"></i> PDF Notes</span>
                    <span class="badge" style="background: rgba(255,255,255,0.05); border: 1px solid var(--dark-border); padding: 8px 16px;"><i class="fas fa-video text-info"></i> Video Lectures</span>
                    <span class="badge" style="background: rgba(255,255,255,0.05); border: 1px solid var(--dark-border); padding: 8px 16px;"><i class="fas fa-link text-primary"></i> Resource Links</span>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="animate-in delay-2">
            <h2 class="section-title">About the Channel</h2>
            <div class="card mt-4 text-center">
                @if($course->channel->logo)
                    <img src="{{ Storage::url($course->channel->logo) }}" alt="Logo" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 16px; border: 2px solid var(--primary);">
                @else
                    <div style="font-size: 3rem; color: var(--primary); margin-bottom: 16px;"><i class="fas fa-tv"></i></div>
                @endif
                <h3 style="font-size: 1.1rem;"><a href="{{ route('channel.profile', $course->channel->slug) }}" style="color: white; text-decoration: none;">{{ $course->channel->name }}</a></h3>
                @if($course->channel->is_verified)
                    <div class="badge badge-primary mt-2"><i class="fas fa-check-circle"></i> Verified</div>
                @endif
                <p style="font-size: 0.9rem; color: var(--text-secondary); margin-top: 16px; line-height: 1.5;">
                    {{ Str::limit($course->channel->description, 150) }}
                </p>
                <a href="{{ route('channel.profile', $course->channel->slug) }}" class="btn btn-secondary btn-block mt-4">View Full Profile</a>
            </div>
        </div>
    </div>
</div>
@endsection
