@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-section">
            <div class="sidebar-title">Menu</div>
            <a href="{{ route('teacher.dashboard') }}" class="sidebar-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="{{ route('teacher.courses') }}" class="sidebar-link active"><i class="fas fa-book"></i> My Courses</a>
            <a href="{{ route('teacher.materials') }}" class="sidebar-link"><i class="fas fa-file-alt"></i> Study Materials</a>
            <a href="{{ route('teacher.test-series') }}" class="sidebar-link"><i class="fas fa-tasks"></i> Test Series</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header animate-in">
            <div>
                <h1 class="page-title">Manage Courses</h1>
                <p class="page-subtitle">Create and update your course offerings.</p>
            </div>
            <a href="{{ route('teacher.courses.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Create Course</a>
        </div>

        @if($courses->count() > 0)
            <div class="card animate-in delay-1">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Thumbnail</th>
                                <th>Course Title</th>
                                <th>Subject</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                                <tr>
                                    <td>
                                        <div style="width: 60px; height: 40px; border-radius: 6px; overflow: hidden; background: var(--dark-surface);">
                                            @if($course->thumbnail)
                                                <img src="{{ Storage::url($course->thumbnail) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                            @endif
                                        </div>
                                    </td>
                                    <td><div style="font-weight: 600;">{{ $course->title }}</div></td>
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
                                    <td>
                                        <a href="#" class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">{{ $courses->links() }}</div>
        @else
            <div class="empty-state card animate-in delay-1">
                <i class="fas fa-book-open"></i>
                <h3>No courses created yet</h3>
                <p>Start teaching by creating your first course.</p>
                <a href="{{ route('teacher.courses.create') }}" class="btn btn-primary mt-3">Create First Course</a>
            </div>
        @endif
    </main>
</div>
@endsection
