@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-section">
            <div class="sidebar-title">Menu</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="{{ route('admin.users') }}" class="sidebar-link"><i class="fas fa-users"></i> Users</a>
            <a href="{{ route('admin.channels') }}" class="sidebar-link"><i class="fas fa-tv"></i> Channels</a>
            <a href="{{ route('admin.courses') }}" class="sidebar-link active"><i class="fas fa-book"></i> Courses</a>
            <a href="{{ route('admin.subjects') }}" class="sidebar-link"><i class="fas fa-tags"></i> Subjects</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header animate-in">
            <div>
                <h1 class="page-title">Global Courses</h1>
                <p class="page-subtitle">Overview of all courses across all vendors.</p>
            </div>
        </div>

        <div class="card animate-in delay-1">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Channel</th>
                            <th>Subject</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td>
                                    <div style="font-weight: 600;"><a href="{{ route('student.course.detail', $course->slug) }}" style="color: var(--text-primary); text-decoration: none;">{{ Str::limit($course->title, 40) }}</a></div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $course->duration_days }} Days</div>
                                </td>
                                <td><a href="{{ route('channel.profile', $course->channel->slug) }}">{{ $course->channel->name }}</a></td>
                                <td><span class="badge badge-info">{{ $course->subject->name }}</span></td>
                                <td>
                                    @if($course->is_free) <span class="badge badge-success">Free</span>
                                    @else ${{ $course->price }}
                                    @endif
                                </td>
                                <td>
                                    @if($course->is_published) <span class="badge badge-success">Published</span>
                                    @else <span class="badge badge-warning">Draft</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center" style="padding: 20px; color: var(--text-muted);"><i class="fas fa-book mb-2" style="font-size: 2rem;"></i><br>No courses uploaded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $courses->links() }}</div>
    </main>
</div>
@endsection
