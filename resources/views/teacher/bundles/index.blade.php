@extends('layouts.app')

@section('content')
<div class="container py-5 animate-in">
    <div class="page-header d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="page-title">Course Bundles & Collaboration</h1>
            <p class="page-subtitle">Collaborate with other teachers to create premium bundled offers.</p>
        </div>
        <a href="{{ route('teacher.bundles.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i> Create New Bundle</a>
    </div>

    <!-- Collaboration Invites -->
    @if($receivedCollaborations->count() > 0)
    <div class="alert alert-info p-4 mb-5 shadow-sm border-0" style="border-left: 5px solid #3b82f6 !important;">
        <h4 class="alert-heading"><i class="fas fa-handshake me-2"></i> Collaboration Invitations</h4>
        <p>Other teachers want to include your expertise in their bundles. Review and accept below:</p>
        <hr>
        <div class="grid-2">
            @foreach($receivedCollaborations as $collab)
            <div class="card p-3 bg-white border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $collab->bundle->title }}</strong>
                        <div class="small text-muted">Proposed Share: {{ $collab->revenue_share_percentage }}%</div>
                    </div>
                    <form action="{{ route('teacher.bundles.collaboration.accept', $collab->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-success btn-sm">Accept & Join</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid-3">
        @foreach($bundles as $bundle)
        <div class="card course-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between mb-3">
                    <span class="badge {{ $bundle->is_published ? 'badge-success' : 'badge-warning' }}">
                        {{ $bundle->is_published ? 'Published' : 'Draft' }}
                    </span>
                    <span class="text-primary font-weight-bold">₹{{ $bundle->bundle_price }}</span>
                </div>
                <h3 class="h5 mb-2">{{ $bundle->title }}</h3>
                <p class="text-muted small mb-4">{{ Str::limit($bundle->description, 80) }}</p>
                
                <div class="mb-4">
                    <div class="small text-muted mb-2">Included Courses:</div>
                    @foreach($bundle->courses as $course)
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-check-circle text-success me-2 small"></i>
                            <span class="small">{{ $course->title }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-between align-items-center border-top pt-3">
                    <div class="avatars-overlap">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($bundle->creator->name) }}&background=random" class="avatar-xs" title="Creator: {{ $bundle->creator->name }}">
                        @foreach($bundle->collaborations->where('status', 'accepted') as $collab)
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($collab->teacher->name) }}&background=random" class="avatar-xs" title="Collaborator: {{ $collab->teacher->name }}">
                        @endforeach
                    </div>
                    <a href="#" class="btn btn-outline btn-sm">Edit Bundle</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($bundles->isEmpty())
    <div class="card text-center p-5 border-dashed">
        <div class="mb-3"><i class="fas fa-box-open fa-3x text-muted"></i></div>
        <h3>No bundles created yet</h3>
        <p class="text-muted">Bundles allow you to offer multiple courses together at a discount, increasing your sales.</p>
        <a href="{{ route('teacher.bundles.create') }}" class="btn btn-primary mt-3">Create First Bundle</a>
    </div>
    @endif
</div>

<style>
    .avatars-overlap {
        display: flex;
        padding-left: 10px;
    }
    .avatar-xs {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 2px solid white;
        margin-left: -10px;
    }
    .border-dashed { border: 2px dashed #cbd5e1; background: transparent; }
</style>
@endsection
