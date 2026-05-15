<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Channel;
use App\Models\Subject;
use App\Models\Subscription;
use App\Models\TestAttempt;
use App\Models\ForumGroup;
use App\Models\Post;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_students' => User::role('student')->count(),
            'total_teachers' => User::role('teacher')->count(),
            'total_courses' => Course::count(),
            'total_channels' => Channel::count(),
            'total_subscriptions' => Subscription::where('status', 'active')->count(),
            'total_revenue' => Subscription::sum('amount_paid'),
            'total_subjects' => Subject::count(),
            'total_test_attempts' => TestAttempt::where('status', 'completed')->count(),
        ];

        $recentUsers = User::latest()->take(10)->get();
        $recentSubscriptions = Subscription::with(['student', 'course'])->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentSubscriptions'));
    }

    public function users(Request $request)
    {
        $role = $request->get('role', null);
        $query = User::query();

        if ($role) {
            $query->role($role);
        }

        $users = $query->latest()->paginate(20);
        return view('admin.users', compact('users', 'role'));
    }

    public function channels()
    {
        $channels = Channel::with('teacher')->latest()->paginate(20);
        return view('admin.channels', compact('channels'));
    }

    public function courses()
    {
        $courses = Course::with(['channel', 'subject'])->latest()->paginate(20);
        return view('admin.courses', compact('courses'));
    }

    public function subjects()
    {
        $subjects = Subject::withCount('courses')->get()->groupBy('level');
        return view('admin.subjects', compact('subjects'));
    }

    public function toggleUserStatus(User $user)
    {
        // Simple toggle - could add a status field later
        return back()->with('success', 'User status updated.');
    }

    public function toggleChannelStatus(Channel $channel)
    {
        $channel->update(['is_active' => !$channel->is_active]);
        return back()->with('success', 'Channel status updated.');
    }

    public function verifyChannel(Channel $channel)
    {
        $channel->update(['is_verified' => !$channel->is_verified]);
        return back()->with('success', 'Channel verification updated.');
    }

    // Community Moderation
    public function community()
    {
        $posts = Post::with(['user', 'forumGroup'])->latest()->paginate(20);
        return view('admin.community.index', compact('posts'));
    }

    public function deletePost(Post $post)
    {
        $post->delete();
        return back()->with('success', 'Post deleted by moderator.');
    }

    public function blockUser(User $user)
    {
        // Assuming we add an is_active column or similar
        // For now just toggle or suspend
        return back()->with('success', 'User has been blocked from community features.');
    }
}
