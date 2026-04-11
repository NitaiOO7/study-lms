@extends('layouts.app')

@push('styles')
<style>
    .comment-card {
        background: var(--dark-surface);
        border: 1px solid var(--dark-border);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
    }
    .comment-card.is-answer {
        border-color: var(--success);
        background: rgba(16,185,129,0.05);
    }
    .reply-card {
        background: rgba(0,0,0,0.2);
        border-radius: 8px;
        padding: 16px;
        margin-top: 16px;
        margin-left: 40px;
        border-left: 3px solid var(--dark-border);
    }
</style>
@endpush

@section('content')
<div class="container animate-in" style="max-width: 800px; padding-top: 40px; padding-bottom: 60px;">
    
    <a href="{{ route('community.group', $post->forumGroup->slug) }}" style="color: var(--text-secondary); text-decoration: none; margin-bottom: 20px; display: inline-block;">&larr; Back to {{ $post->forumGroup->name }}</a>
    
    <!-- Main Post -->
    <div class="post-card" style="padding: 30px; border-radius: 16px;">
        <div class="post-header">
            <div class="avatar" style="width: 50px; height: 50px; font-size: 1.2rem;">{{ substr($post->user->name, 0, 1) }}</div>
            <div>
                <div class="post-author" style="font-size: 1.1rem;">{{ $post->user->name }} {!! $post->user->hasRole('teacher') ? '<span class="badge badge-success">Teacher</span>' : '' !!}</div>
                <div class="post-time">{{ $post->created_at->format('M d, Y \\a\\t h:i A') }}</div>
            </div>
            @if($post->is_solved)
                <div class="badge badge-success" style="margin-left: auto; padding: 6px 12px;"><i class="fas fa-check-circle"></i> Solved</div>
            @endif
        </div>
        
        <h1 style="font-size: 1.6rem; margin-bottom: 16px;">{{ $post->title }}</h1>
        <div class="post-body" style="font-size: 1.05rem; color: var(--text-primary);">{!! nl2br(e($post->body)) !!}</div>
        
        @if($post->image)
            <div style="margin-bottom: 20px; border-radius: 12px; overflow: hidden; border: 1px solid var(--dark-border);">
                <img src="{{ Storage::url($post->image) }}" style="max-width: 100%;">
            </div>
        @endif

        <div class="post-actions" style="border-top: 1px solid var(--dark-border); padding-top: 16px; margin-top: 20px;">
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
            
            <span class="post-action"><i class="fas fa-comment"></i> {{ $post->all_comments_count }} Comments</span>
        </div>
    </div>

    <!-- Add Comment -->
    @auth
        <div class="card mb-5">
            <h3 style="font-size: 1.1rem; margin-bottom: 16px;">Leave a Comment</h3>
            <form action="{{ route('community.comment.store', $post->id) }}" method="POST">
                @csrf
                <textarea name="body" class="form-textarea" style="min-height: 100px; margin-bottom: 16px;" placeholder="Write your response..." required></textarea>
                <div style="text-align: right;">
                    <button type="submit" class="btn btn-primary">Post Comment</button>
                </div>
            </form>
        </div>
    @endauth

    <!-- Comments List -->
    <div>
        <h3 style="margin-bottom: 24px;">{{ $post->comments->count() }} Comments</h3>
        
        @foreach($post->comments as $comment)
            <div class="comment-card {{ $comment->is_answer ? 'is-answer' : '' }}">
                @if($comment->is_answer)
                    <div class="badge badge-success mb-3"><i class="fas fa-check-circle"></i> Marked as Answer</div>
                @endif
                <div class="post-header" style="margin-bottom: 12px;">
                    <div class="avatar" style="width: 36px; height: 36px;">{{ substr($comment->user->name, 0, 1) }}</div>
                    <div>
                        <div class="post-author">{{ $comment->user->name }}</div>
                        <div class="post-time">{{ $comment->created_at->diffForHumans() }}</div>
                    </div>
                    
                    <!-- Mark as Answer button (Post Author Only) -->
                    @auth
                        @if(auth()->id() === $post->user_id && !$comment->is_answer)
                            <form action="{{ route('community.comment.answer', $comment->id) }}" method="POST" style="margin-left: auto;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Mark Correct</button>
                            </form>
                        @endif
                    @endauth
                </div>
                
                <div class="post-body" style="margin-bottom: 12px;">{!! nl2br(e($comment->body)) !!}</div>
                
                <!-- Replies -->
                @if($comment->replies->count() > 0)
                    @foreach($comment->replies as $reply)
                        <div class="reply-card">
                            <div class="post-header" style="margin-bottom: 8px;">
                                <div class="avatar" style="width: 28px; height: 28px; font-size: 0.7rem;">{{ substr($reply->user->name, 0, 1) }}</div>
                                <div>
                                    <div class="post-author" style="font-size: 0.85rem;">{{ $reply->user->name }}</div>
                                    <div class="post-time" style="font-size: 0.75rem;">{{ $reply->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div style="font-size: 0.9rem; color: var(--text-secondary);">{!! nl2br(e($reply->body)) !!}</div>
                        </div>
                    @endforeach
                @endif
                
                <!-- Add Reply Form -->
                @auth
                    <div style="margin-top: 16px; margin-left: 40px;">
                        <button class="btn btn-sm" style="background:transparent; color: var(--text-muted); border: 1px solid var(--dark-border);" onclick="document.getElementById('reply-form-{{ $comment->id }}').style.display='block'; this.style.display='none';"><i class="fas fa-reply"></i> Reply</button>
                        
                        <form action="{{ route('community.comment.store', $post->id) }}" method="POST" id="reply-form-{{ $comment->id }}" style="display: none; margin-top: 10px;">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                            <div style="display: flex; gap: 10px;">
                                <input type="text" name="body" class="form-input" placeholder="Write a reply..." required style="flex: 1;">
                                <button type="submit" class="btn btn-primary btn-sm">Send</button>
                            </div>
                        </form>
                    </div>
                @endauth
            </div>
        @endforeach
    </div>
</div>
@endsection
