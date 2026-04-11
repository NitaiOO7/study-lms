@extends('layouts.app')

@push('styles')
<style>
    .forum-layout {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 30px;
    }
    @media (max-width: 1024px) {
        .forum-layout { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="container animate-in" style="padding-top: 30px;">
    
    <!-- Group Header -->
    <div class="card mb-4" style="background: linear-gradient(to right, var(--dark-card), rgba(99,102,241,0.1)); border-left: 4px solid var(--primary);">
        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="font-size: 3rem; background: rgba(0,0,0,0.2); width: 80px; height: 80px; border-radius: 16px; display: flex; align-items: center; justify-content: center;">
                {{ $forumGroup->icon ?? ($forumGroup->is_universal ? '🌍' : '📚') }}
            </div>
            <div>
                @if($forumGroup->is_universal)
                    <div class="badge badge-primary mb-2">Global Group</div>
                @else
                    <div class="badge badge-info mb-2">{{ strtoupper($forumGroup->subject->level ?? 'General') }}</div>
                @endif
                <h1 style="font-size: 1.8rem; margin-bottom: 8px;">{{ $forumGroup->name }}</h1>
                <p style="color: var(--text-secondary); max-width: 800px;">{{ $forumGroup->description }}</p>
            </div>
        </div>
    </div>

    <div class="forum-layout">
        <!-- Main Feed -->
        <div>
            <!-- Create Post Box -->
            @auth
                <div class="card mb-4">
                    <form action="{{ route('community.post.store', $forumGroup->slug) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div style="display: flex; gap: 16px;">
                            <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                            <div style="flex: 1;">
                                <input type="text" name="title" class="form-input" style="margin-bottom: 10px;" placeholder="Ask a question or start a discussion..." required>
                                <textarea name="body" class="form-textarea" style="min-height: 80px; margin-bottom: 10px;" placeholder="Add more details..." required></textarea>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="position: relative; overflow: hidden; display: inline-block;">
                                        <button type="button" class="btn btn-secondary btn-sm"><i class="fas fa-image"></i> Add Image</button>
                                        <input type="file" name="image" accept="image/*" style="font-size: 100px; position: absolute; left: 0; top: 0; opacity: 0; cursor: pointer;">
                                    </div>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Post</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            @else
                <div class="card mb-4 text-center" style="padding: 30px;">
                    <p style="color: var(--text-secondary); margin-bottom: 16px;">Join the discussion!</p>
                    <a href="{{ route('login') }}" class="btn btn-primary">Login to Post</a>
                </div>
            @endauth

            <!-- Posts List -->
            @if($posts->count() > 0)
                <div class="posts-list">
                    @foreach($posts as $post)
                        <div class="post-card">
                            <div class="post-header">
                                <div class="avatar">{{ substr($post->user->name, 0, 1) }}</div>
                                <div>
                                    <div class="post-author">{{ $post->user->name }} {!! $post->user->hasRole('teacher') ? '<span class="badge badge-success" style="font-size: 0.6rem; padding: 2px 6px;">Teacher</span>' : '' !!}</div>
                                    <div class="post-time">{{ $post->created_at->diffForHumans() }}</div>
                                </div>
                                @if($post->is_solved)
                                    <div class="badge badge-success" style="margin-left: auto;"><i class="fas fa-check-circle"></i> Solved</div>
                                @endif
                                @if($post->is_pinned)
                                    <div class="badge badge-warning" style="margin-left: {{ $post->is_solved ? '8px' : 'auto' }};"><i class="fas fa-thumbtack"></i> Pinned</div>
                                @endif
                            </div>
                            
                            <a href="{{ route('community.post.show', $post->id) }}" class="post-title-link">{{ $post->title }}</a>
                            <div class="post-body">{{ Str::limit($post->body, 200) }}</div>
                            
                            @if($post->image)
                                <div style="margin-bottom: 16px; border-radius: 12px; overflow: hidden; border: 1px solid var(--dark-border);">
                                    <img src="{{ Storage::url($post->image) }}" alt="Post image" style="width: 100%; max-height: 300px; object-fit: cover;">
                                </div>
                            @endif

                            <div class="post-actions" style="border-top: 1px solid var(--dark-border); padding-top: 12px;">
                                @auth
                                <form action="{{ route('community.like') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="likeable_type" value="post">
                                    <input type="hidden" name="likeable_id" value="{{ $post->id }}">
                                    <button class="post-action {{ $post->isLikedBy(auth()->user()) ? 'text-primary' : '' }}">
                                        <i class="fas fa-thumbs-up"></i> {{ $post->likes_count }} Likes
                                    </button>
                                </form>
                                @else
                                <span class="post-action"><i class="fas fa-thumbs-up"></i> {{ $post->likes_count }} Likes</span>
                                @endauth
                                
                                <a href="{{ route('community.post.show', $post->id) }}" class="post-action"><i class="fas fa-comment"></i> {{ $post->all_comments_count }} Comments</a>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                {{ $posts->links() }}
            @else
                <div class="empty-state card">
                    <i class="fas fa-comments"></i>
                    <h3>No posts yet.</h3>
                    <p>Be the first to start a discussion in this group!</p>
                </div>
            @endif
        </div>

        <!-- Sidebar Widgets -->
        <div>
            <div class="card" style="position: sticky; top: 100px;">
                <h3 style="font-size: 1.1rem; margin-bottom: 16px;">About Group</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 16px; line-height: 1.5;">This is a dedicated space for students and teachers to discuss, share knowledge, and solve problems.</p>
                <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--dark-border); padding-top: 16px; margin-top: 16px;">
                    <div style="text-align: center;">
                        <div style="font-weight: 800; font-size: 1.2rem;">{{ $forumGroup->posts()->count() }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">Posts</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
