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
                        <a href="{{ route('student.learn', $course->slug) }}" class="btn btn-primary btn-block btn-lg mt-3" style="background: linear-gradient(135deg, #8b5cf6, #d946ef); border: none;"><i class="fas fa-play-circle"></i> Enter Learning Room</a>
                        <a href="{{ route('student.test-series', $course->slug) }}" class="btn btn-secondary btn-block btn-lg mt-2"><i class="fas fa-laptop-code"></i> Go to Tests</a>
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

            <div class="mt-5">
                <h3 style="font-size: 1.3rem; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-video text-info"></i> Course Lessons
                </h3>
                
                @if($course->lessons->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @foreach($course->lessons as $lesson)
                        <div style="display: flex; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; padding: 12px; gap: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                            <!-- Thumbnail -->
                            <div style="width: 220px; height: 130px; background: #000; border-radius: 8px; flex-shrink: 0; position: relative;">
                                @if($course->thumbnail)
                                    <img src="{{ Storage::url($course->thumbnail) }}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.9; border-radius: 8px;">
                                @else
                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                        <i class="fas fa-play-circle fa-3x"></i>
                                    </div>
                                @endif
                                <div style="position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.7); color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem;">
                                    Lesson {{ $lesson->sort_order }}
                                </div>
                            </div>
                            
                            <!-- Details -->
                            <div style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
                                <h3 style="font-size: 1.1rem; margin-bottom: 8px; color: #6366f1; font-weight: 600;">{{ $lesson->title }}</h3>
                                <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 16px; display: flex; gap: 16px;">
                                    <span><i class="far fa-clock"></i> 1 hrs 12 mins</span>
                                    <span>Created on: {{ $lesson->created_at->format('d M Y, h:i A') }}</span>
                                </div>
                                
                                <div style="display: flex; gap: 12px;">
                                    @if($isSubscribed || $lesson->is_free)
                                        <a href="{{ route('student.learn', ['course' => $course->slug, 'lesson' => $lesson->id, 'view' => 'video']) }}" class="btn btn-sm" style="background: #8b5cf6; color: white; border-radius: 6px; padding: 6px 16px; text-decoration: none; border: none; font-weight: 500;">
                                            <i class="fas fa-play"></i> Watch
                                        </a>
                                        
                                        @if($lesson->pdf_path)
                                        <a href="{{ route('student.learn', ['course' => $course->slug, 'lesson' => $lesson->id, 'view' => 'clean_pdf']) }}" class="btn btn-sm" style="background: white; border: 1px solid #e5e7eb; color: #8b5cf6; border-radius: 6px; padding: 6px 16px; text-decoration: none; font-weight: 500;">
                                            <i class="far fa-file-pdf"></i> Without Annotation
                                        </a>
                                        @endif
                                        
                                        @if($lesson->annotated_pdf_path)
                                        <a href="{{ route('student.learn', ['course' => $course->slug, 'lesson' => $lesson->id, 'view' => 'annotated_pdf']) }}" class="btn btn-sm" style="background: white; border: 1px solid #e5e7eb; color: #8b5cf6; border-radius: 6px; padding: 6px 16px; text-decoration: none; font-weight: 500;">
                                            <i class="far fa-file-alt"></i> With Annotation
                                        </a>
                                        @endif
                                    @else
                                        <span class="badge" style="background: #f3f4f6; color: #9ca3af; padding: 6px 12px; border-radius: 6px;"><i class="fas fa-lock"></i> Premium Lesson</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="card">
                        <p class="text-muted">No lessons uploaded yet.</p>
                    </div>
                @endif
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
                @if($isSubscribed)
                        <div class="mt-4 pt-3" style="border-top: 1px solid var(--dark-border);">
                            <div style="font-weight: 600; margin-bottom: 8px;"><i class="fas fa-check-circle text-success"></i> Active Subscription</div>
                            <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 16px;">Valid until {{ \Carbon\Carbon::parse($isSubscribed->expires_at ?? now()->addYear())->format('M d, Y') }}</div>
                            <a href="{{ route('student.learn', $course->slug) }}" class="btn btn-primary btn-block mb-2" style="background: linear-gradient(135deg, #8b5cf6, #d946ef); border: none;"><i class="fas fa-play-circle"></i> Enter Learning Room</a>
                            <a href="{{ route('student.test-series', $course->slug) }}" class="btn btn-secondary btn-block"><i class="fas fa-laptop-code"></i> Access Test Series</a>
                        </div>
                @else
                    <a href="{{ route('channel.profile', $course->channel->slug) }}" class="btn btn-secondary btn-block mt-4">View Full Profile</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
