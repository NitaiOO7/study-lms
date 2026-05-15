@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="page-header mb-5">
        <h1 class="page-title">Community Moderation</h1>
        <p class="page-subtitle">Monitor posts and comments across all forums.</p>
    </div>

    <div class="card p-0">
        <div class="table-container">
            <table class="m-0">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Forum Group</th>
                        <th>Post Content</th>
                        <th>Engagement</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts as $post)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2">{{ substr($post->user->name, 0, 1) }}</div>
                                <div>
                                    <div class="font-weight-bold">{{ $post->user->name }}</div>
                                    <small class="text-muted">{{ $post->user->getRoleNames()->first() }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-light text-dark">{{ $post->forumGroup->name }}</span>
                            <div class="small text-muted">{{ $post->forumGroup->type }}</div>
                        </td>
                        <td style="max-width: 300px;">
                            <div class="font-weight-bold">{{ $post->title }}</div>
                            <div class="text-truncate small text-muted">{{ $post->body }}</div>
                        </td>
                        <td>
                            <div class="small"><i class="far fa-heart me-1"></i> {{ $post->likes_count }}</div>
                            <div class="small"><i class="far fa-comment me-1"></i> {{ $post->comments_count }}</div>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('community.post.show', $post->id) }}" class="btn btn-outline btn-sm" target="_blank"><i class="fas fa-eye"></i></a>
                                <form action="{{ route('admin.community.post.delete', $post->id) }}" method="POST" onsubmit="return confirm('Delete this post?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            {{ $posts->links() }}
        </div>
    </div>
</div>
@endsection
