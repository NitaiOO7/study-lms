@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-section">
            <div class="sidebar-title">Menu</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="{{ route('admin.users') }}" class="sidebar-link active"><i class="fas fa-users"></i> Users</a>
            <a href="{{ route('admin.channels') }}" class="sidebar-link"><i class="fas fa-tv"></i> Channels</a>
            <a href="{{ route('admin.courses') }}" class="sidebar-link"><i class="fas fa-book"></i> Courses</a>
            <a href="{{ route('admin.subjects') }}" class="sidebar-link"><i class="fas fa-tags"></i> Subjects</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header animate-in">
            <div>
                <h1 class="page-title">Manage Users</h1>
                <p class="page-subtitle">View and moderate all students and teachers.</p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card mb-4 animate-in delay-1" style="padding: 16px;">
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('admin.users') }}" class="btn {{ !$role ? 'btn-primary' : 'btn-secondary' }}">All Users</a>
                <a href="{{ route('admin.users', ['role' => 'student']) }}" class="btn {{ $role == 'student' ? 'btn-primary' : 'btn-secondary' }}">Students</a>
                <a href="{{ route('admin.users', ['role' => 'teacher']) }}" class="btn {{ $role == 'teacher' ? 'btn-primary' : 'btn-secondary' }}">Teachers</a>
                <a href="{{ route('admin.users', ['role' => 'admin']) }}" class="btn {{ $role == 'admin' ? 'btn-primary' : 'btn-secondary' }}">Admins</a>
            </div>
        </div>

        @if($users->count() > 0)
            <div class="card animate-in delay-2">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <div class="avatar" style="width: 32px; height: 32px; font-size: 0.8rem;">{{ substr($user->name, 0, 1) }}</div>
                                            <div style="font-weight: 600;">{{ $user->name }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->hasRole('admin')) <span class="badge badge-danger">Admin</span>
                                        @elseif($user->hasRole('teacher')) <span class="badge badge-success">Teacher</span>
                                        @else <span class="badge badge-primary">Student</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <form action="#" method="POST" style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-ban"></i> Toggle Status</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">{{ $users->appends(['role' => $role])->links() }}</div>
        @else
            <div class="empty-state card animate-in delay-2">
                <i class="fas fa-users-slash"></i>
                <h3>No users found</h3>
            </div>
        @endif
    </main>
</div>
@endsection
