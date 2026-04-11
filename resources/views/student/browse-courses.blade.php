@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-section">
            <div class="sidebar-title">Menu</div>
            <a href="{{ route('student.dashboard') }}" class="sidebar-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="{{ route('student.browse') }}" class="sidebar-link active"><i class="fas fa-compass"></i> Browse Courses</a>
            <a href="{{ route('student.my-courses') }}" class="sidebar-link"><i class="fas fa-book-reader"></i> My Learning</a>
        </div>
        
        <div class="sidebar-section mt-4">
            <div class="sidebar-title">Filter by Level</div>
            <form action="{{ route('student.browse') }}" method="GET" id="filter-form">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                @if(request('subject')) <input type="hidden" name="subject" value="{{ request('subject') }}"> @endif
                
                <a href="javascript:void(0)" onclick="document.getElementById('level-input').value=''; document.getElementById('filter-form').submit();" class="sidebar-link {{ !request('level') ? 'active' : '' }}">All Levels</a>
                <a href="javascript:void(0)" onclick="document.getElementById('level-input').value='hs'; document.getElementById('filter-form').submit();" class="sidebar-link {{ request('level') == 'hs' ? 'active' : '' }}">High School</a>
                <a href="javascript:void(0)" onclick="document.getElementById('level-input').value='graduate'; document.getElementById('filter-form').submit();" class="sidebar-link {{ request('level') == 'graduate' ? 'active' : '' }}">Graduate</a>
                <a href="javascript:void(0)" onclick="document.getElementById('level-input').value='master'; document.getElementById('filter-form').submit();" class="sidebar-link {{ request('level') == 'master' ? 'active' : '' }}">Master</a>
                <input type="hidden" name="level" id="level-input" value="{{ request('level') }}">
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header animate-in">
            <div>
                <h1 class="page-title">Browse Premium Courses</h1>
                <p class="page-subtitle">Discover the right course for your academic goals.</p>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="card mb-4 animate-in delay-1" style="padding: 16px;">
            <form action="{{ route('student.browse') }}" method="GET" style="display: flex; gap: 16px; flex-wrap: wrap;">
                @if(request('level')) <input type="hidden" name="level" value="{{ request('level') }}"> @endif
                
                <div style="flex: 1; min-width: 250px;">
                    <div style="position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                        <input type="text" name="search" class="form-input" placeholder="Search for courses, skills, or teachers..." value="{{ request('search') }}" style="padding-left: 45px; border-radius: 30px;">
                    </div>
                </div>
                
                <div style="width: 200px;">
                    <select name="subject" class="form-select" style="border-radius: 30px;">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $level => $levelSubjects)
                            <optgroup label="{{ strtoupper($level) }}">
                                @foreach($levelSubjects as $subject)
                                    <option value="{{ $subject->id }}" {{ request('subject') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary" style="border-radius: 30px;">Filter</button>
                @if(request('search') || request('subject') || request('level'))
                    <a href="{{ route('student.browse') }}" class="btn btn-secondary" style="border-radius: 30px;">Clear</a>
                @endif
            </form>
        </div>

        @if($courses->count() > 0)
            <div class="grid-3 animate-in delay-2">
                @foreach($courses as $course)
                    <div class="course-card">
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
                                <span><i class="fas fa-tv text-primary"></i> <a href="{{ route('channel.profile', $course->channel->slug) }}">{{ $course->channel->name }}</a></span>
                            </div>
                            <h3 class="course-title"><a href="{{ route('student.course.detail', $course->slug) }}">{{ Str::limit($course->title, 50) }}</a></h3>
                            <div class="course-meta" style="margin-bottom: 16px;">
                                <span class="badge badge-info">{{ $course->subject->name }}</span>
                                <span><i class="fas fa-clock text-warning"></i> {{ $course->duration_days }} Days</span>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div class="course-price {{ $course->is_free ? 'free' : '' }}">
                                    {{ $course->is_free ? 'Free' : '$' . $course->price }}
                                </div>
                                <a href="{{ route('student.course.detail', $course->slug) }}" class="btn btn-secondary btn-sm">Enroll Now</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div style="margin-top: 32px;" class="animate-in delay-3">
                {{ $courses->withQueryString()->links() }}
            </div>
        @else
            <div class="empty-state card animate-in delay-2">
                <i class="fas fa-search"></i>
                <h3>No courses found.</h3>
                <p>Try adjusting your search criteria or check back later.</p>
            </div>
        @endif
    </main>
</div>
@endsection
