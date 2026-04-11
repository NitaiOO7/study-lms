@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-section">
            <div class="sidebar-title">Menu</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="{{ route('admin.users') }}" class="sidebar-link"><i class="fas fa-users"></i> Users</a>
            <a href="{{ route('admin.channels') }}" class="sidebar-link"><i class="fas fa-tv"></i> Channels</a>
            <a href="{{ route('admin.courses') }}" class="sidebar-link"><i class="fas fa-book"></i> Courses</a>
            <a href="{{ route('admin.subjects') }}" class="sidebar-link active"><i class="fas fa-tags"></i> Subjects</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header animate-in">
            <div>
                <h1 class="page-title">Subject Master Setup</h1>
                <p class="page-subtitle">Core curricular subjects defined across the LMS.</p>
            </div>
        </div>

        <div class="grid-3 animate-in delay-1">
            @foreach($subjects as $level => $levelSubjects)
                <div class="card">
                    <h3 style="margin-bottom: 16px; font-size: 1.2rem; text-transform: uppercase;">{{ $level }} Level</h3>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach($levelSubjects as $subject)
                            <div style="display: flex; justify-content: space-between; padding: 12px; background: var(--dark-surface); border: 1px solid var(--dark-border); border-radius: 8px;">
                                <div>
                                    <div style="font-weight: 600;">{{ $subject->icon }} {{ $subject->name }}</div>
                                </div>
                                <div class="badge badge-primary">{{ $subject->courses_count }} Courses</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </main>
</div>
@endsection
