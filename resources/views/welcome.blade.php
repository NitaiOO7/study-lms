@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container animate-in">
        <div class="badge badge-primary" style="margin-bottom: 20px;">
            <i class="fas fa-star"></i> #1 Premium Learning Platform
        </div>
        <h1 class="hero-title pt-4">Unlock Your True Potential<br>With EduVerse</h1>
        <p class="hero-subtitle">
            Join thousands of students achieving top ranks. Access premium courses, expert-curated test series, and a vibrant community of learners.
        </p>
        <div class="hero-actions">
            @guest
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-rocket"></i> Start Learning Now
                </a>
                <a href="#courses" class="btn btn-secondary btn-lg">
                    <i class="fas fa-compass"></i> Explore Courses
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                </a>
            @endguest
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="section-block" style="background: rgba(15,15,35,0.5);">
    <div class="container">
        <div class="grid-4 animate-in delay-1">
            <div class="stat-card" style="justify-content: center; flex-direction: column; text-align: center;">
                <div class="stat-icon" style="background: var(--gradient-primary); margin-bottom: 10px;"><i class="fas fa-users"></i></div>
                <div class="stat-value">50K+</div>
                <div class="stat-label">Active Learners</div>
            </div>
            <div class="stat-card" style="justify-content: center; flex-direction: column; text-align: center;">
                <div class="stat-icon" style="background: var(--gradient-success); margin-bottom: 10px;"><i class="fas fa-book"></i></div>
                <div class="stat-value">1,200+</div>
                <div class="stat-label">Premium Courses</div>
            </div>
            <div class="stat-card" style="justify-content: center; flex-direction: column; text-align: center;">
                <div class="stat-icon" style="background: var(--gradient-accent); margin-bottom: 10px;"><i class="fas fa-tasks"></i></div>
                <div class="stat-value">10K+</div>
                <div class="stat-label">Test Series</div>
            </div>
            <div class="stat-card" style="justify-content: center; flex-direction: column; text-align: center;">
                <div class="stat-icon" style="background: linear-gradient(135deg, #ec4899, #8b5cf6); margin-bottom: 10px;"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="stat-value">500+</div>
                <div class="stat-label">Expert Teachers</div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Courses -->
<section id="courses" class="section-block">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 32px;"><div>
            <h2 class="section-title animate-in delay-2">Featured Courses</h2>
            <p class="section-subtitle mb-0 animate-in delay-2">Explore the most popular courses tailored for your success.</p>
        </div></div>

        @if($featuredCourses->count() > 0)
            <div class="grid-4">
                @foreach($featuredCourses as $course)
                    <div class="course-card animate-in delay-3">
                        <div class="course-thumb">
                            @if($course->thumbnail)
                                <img src="{{ Storage::url($course->thumbnail) }}" alt="{{ $course->title }}">
                            @else
                                <i class="fas fa-book-open"></i>
                            @endif
                            <div style="position: absolute; top: 12px; right: 12px;" class="badge badge-primary">{{ strtoupper($course->level) }}</div>
                        </div>
                        <div class="course-body">
                            <div class="course-meta">
                                <span><i class="fas fa-folder text-primary"></i> {{ $course->subject->name }}</span>
                                <span><i class="fas fa-clock text-warning"></i> {{ $course->duration_days }} Days</span>
                            </div>
                            <h3 class="course-title"><a href="{{ route('student.course.detail', $course->slug) }}">{{ Str::limit($course->title, 50) }}</a></h3>
                            <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 16px;">By {{ $course->channel->teacher->name }}</p>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div class="course-price {{ $course->is_free ? 'free' : '' }}">
                                    {{ $course->is_free ? 'Free' : '$' . $course->price }}
                                </div>
                                <a href="{{ route('student.course.detail', $course->slug) }}" class="btn btn-secondary btn-sm">View Details</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state card">
                <i class="fas fa-box-open"></i>
                <h3>No courses available yet.</h3>
                <p>Check back later for exciting new content.</p>
            </div>
        @endif
    </div>
</section>

<!-- Demo Test Series -->
<section class="section-block" style="background: rgba(15,15,35,0.5);">
    <div class="container animate-in delay-4">
        <h2 class="section-title text-center">Try Our Demo Test Series</h2>
        <p class="section-subtitle text-center mb-5">Experience our robust testing engine with these free demos.</p>

        @if($demoTestSeries->count() > 0)
            <div class="grid-4 mt-4">
                @foreach($demoTestSeries as $series)
                    <div class="card card-glass text-center">
                        <div style="font-size: 2.5rem; color: var(--primary-light); margin-bottom: 16px;"><i class="fas fa-clipboard-check"></i></div>
                        <h3 style="font-size: 1.1rem; margin-bottom: 8px;">{{ $series->title }}</h3>
                        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 16px;">{{ $series->course->subject->name }} | {{ $series->total_marks }} Marks</p>
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm btn-block">Login to Attempt</a>
                        @else
                            <a href="{{ route('student.test-series', $series->course->slug) }}" class="btn btn-primary btn-sm btn-block">Go to Course</a>
                        @endguest
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-vial"></i>
                <h3>No demo tests currently.</h3>
            </div>
        @endif
    </div>
</section>
@endsection
