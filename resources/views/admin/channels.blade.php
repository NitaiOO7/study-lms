@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-section">
            <div class="sidebar-title">Menu</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="{{ route('admin.users') }}" class="sidebar-link"><i class="fas fa-users"></i> Users</a>
            <a href="{{ route('admin.channels') }}" class="sidebar-link active"><i class="fas fa-tv"></i> Channels</a>
            <a href="{{ route('admin.courses') }}" class="sidebar-link"><i class="fas fa-book"></i> Courses</a>
            <a href="{{ route('admin.subjects') }}" class="sidebar-link"><i class="fas fa-tags"></i> Subjects</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header animate-in">
            <div>
                <h1 class="page-title">Manage Channels (Teachers)</h1>
                <p class="page-subtitle">Verify multi-vendor teacher portals.</p>
            </div>
        </div>

        <div class="card animate-in delay-1">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Channel Name</th>
                            <th>Teacher</th>
                            <th>Courses</th>
                            <th>Verification</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($channels as $channel)
                            <tr>
                                <td>
                                    <div style="font-weight: 600;"><a href="{{ route('channel.profile', $channel->slug) }}" style="color: var(--text-primary); text-decoration: none;">{{ $channel->name }}</a></div>
                                </td>
                                <td>{{ $channel->teacher->name }}</td>
                                <td>{{ $channel->courses()->count() }}</td>
                                <td>
                                    @if($channel->is_verified)
                                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Verified</span>
                                    @else
                                        <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($channel->is_active)
                                        <span class="badge badge-primary">Active</span>
                                    @else
                                        <span class="badge badge-danger">Disabled</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <form action="{{ route('admin.channel.verify', $channel->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn {{ $channel->is_verified ? 'btn-secondary' : 'btn-success' }} btn-sm">
                                                {{ $channel->is_verified ? 'Unverify' : 'Verify' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.channel.toggle', $channel->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary btn-sm">
                                                {{ $channel->is_active ? 'Disable' : 'Enable' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center" style="padding: 20px; color: var(--text-muted);"><i class="fas fa-tv mb-2" style="font-size: 2rem;"></i><br>No channels created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $channels->links() }}</div>
    </main>
</div>
@endsection
