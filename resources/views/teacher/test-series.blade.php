@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-section">
            <div class="sidebar-title">Menu</div>
            <a href="{{ route('teacher.dashboard') }}" class="sidebar-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="{{ route('teacher.courses') }}" class="sidebar-link"><i class="fas fa-book"></i> My Courses</a>
            <a href="{{ route('teacher.materials') }}" class="sidebar-link"><i class="fas fa-file-alt"></i> Study Materials</a>
            <a href="{{ route('teacher.test-series') }}" class="sidebar-link active"><i class="fas fa-tasks"></i> Test Series</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header animate-in">
            <div>
                <h1 class="page-title">Manage Test Series</h1>
                <p class="page-subtitle">Build mock tests, mock exams, and demo tests.</p>
            </div>
            <a href="{{ route('teacher.test-series.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Create Test Series</a>
        </div>

        @if($testSeries->count() > 0)
            <div class="card animate-in delay-1">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Test Series Title</th>
                                <th>Course</th>
                                <th>Sections</th>
                                <th>Marks</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($testSeries as $series)
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;">{{ $series->title }}</div>
                                        @if($series->is_demo) <div class="badge badge-success mt-1">Demo Test</div> @endif
                                    </td>
                                    <td>{{ Str::limit($series->course->title, 30) }}</td>
                                    <td><span class="badge badge-primary">{{ $series->sections->count() }} Sections</span></td>
                                    <td>{{ $series->total_marks }} (Pass: {{ $series->passing_marks }})</td>
                                    <td>
                                        @if($series->is_published) <span class="badge badge-success">Published</span>
                                        @else <span class="badge badge-warning">Draft</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('teacher.test-series.sections', $series->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-layer-group"></i> Sections</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">{{ $testSeries->links() }}</div>
        @else
            <div class="empty-state card animate-in delay-1">
                <i class="fas fa-tasks"></i>
                <h3>No Test Series Available</h3>
                <p>Create dynamic, multi-section test series for your students.</p>
                <a href="{{ route('teacher.test-series.create') }}" class="btn btn-primary mt-3">Create Test Series</a>
            </div>
        @endif
    </main>
</div>
@endsection
