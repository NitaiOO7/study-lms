<?php

namespace App\Http\Controllers;

use App\Models\ForumGroup;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommunityController extends Controller
{
    // Forum Groups List
    public function index()
    {
        $universalGroup = ForumGroup::where('is_universal', true)->first();
        $subjectGroups = ForumGroup::where('is_universal', false)->active()->with('subject')->get();

        return view('community.index', compact('universalGroup', 'subjectGroups'));
    }

    // View Forum Group
    public function showGroup(ForumGroup $forumGroup)
    {
        $posts = $forumGroup->posts()
            ->with(['user', 'comments.user'])
            ->withCount('allComments')
            ->latest()
            ->paginate(15);

        return view('community.group', compact('forumGroup', 'posts'));
    }

    // Create Post
    public function storePost(Request $request, ForumGroup $forumGroup)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|image|max:5120',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'forum_group_id' => $forumGroup->id,
            'title' => $request->title,
            'body' => $request->body,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        Post::create($data);
        return back()->with('success', 'Post published successfully!');
    }

    // View Post Detail
    public function showPost(Post $post)
    {
        $post->load(['user', 'forumGroup', 'comments.user', 'comments.replies.user']);
        return view('community.post', compact('post'));
    }

    // Add Comment
    public function storeComment(Request $request, Post $post)
    {
        $request->validate([
            'body' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
            'image' => 'nullable|image|max:5120',
        ]);

        $data = [
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'body' => $request->body,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('comments', 'public');
        }

        Comment::create($data);

        // Update comments count
        $post->update(['comments_count' => $post->allComments()->count()]);

        return back()->with('success', 'Comment added!');
    }

    // Mark as Answer
    public function markAsAnswer(Comment $comment)
    {
        $post = $comment->post;

        if ($post->user_id !== Auth::id()) {
            abort(403, 'Only the post author can mark an answer.');
        }

        // Unmark any existing answer
        $post->allComments()->update(['is_answer' => false]);

        $comment->update(['is_answer' => true]);
        $post->update(['is_solved' => true]);

        return back()->with('success', 'Marked as best answer!');
    }

    // Toggle Like
    public function toggleLike(Request $request)
    {
        $request->validate([
            'likeable_type' => 'required|in:post,comment',
            'likeable_id' => 'required|integer',
        ]);

        $type = $request->likeable_type === 'post' ? Post::class : Comment::class;
        $model = $type::findOrFail($request->likeable_id);

        $existing = Like::where('user_id', Auth::id())
            ->where('likeable_type', $type)
            ->where('likeable_id', $model->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $model->decrement('likes_count');
            $liked = false;
        } else {
            Like::create([
                'user_id' => Auth::id(),
                'likeable_type' => $type,
                'likeable_id' => $model->id,
            ]);
            $model->increment('likes_count');
            $liked = true;
        }

        return back()->with('liked', $liked);
    }
}
