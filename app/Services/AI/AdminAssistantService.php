<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\Course;
use App\Models\Channel;
use App\Models\Subscription;
use App\Models\TestAttempt;
use App\Models\AiChatSession;
use Illuminate\Support\Facades\DB;

/**
 * AdminAssistantService
 *
 * Handles AI responses for the Admin role.
 * Can query the live database to provide real-time platform analytics.
 * All database queries are read-only and pre-approved — no raw SQL from user input.
 */
class AdminAssistantService
{
    public function __construct(
        protected GroqService            $groq,
        protected KnowledgeSearchService $search
    ) {}

    public function respond(string $message, AiChatSession $session, User $user): string
    {
        // Fetch live platform stats
        $stats        = $this->getLiveStats();
        $systemPrompt = $this->buildSystemPrompt($stats);

        $messages = array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $session->getApiHistory(config('ai.history_limit', 10)),
            [['role' => 'user', 'content' => $message]]
        );

        $result = $this->groq->chat($messages);
        return $result['content'];
    }

    /**
     * Fetch real-time platform statistics from the database.
     * All queries here are safe, read-only, and pre-approved.
     */
    protected function getLiveStats(): array
    {
        $totalRevenue     = Subscription::sum('amount_paid');
        $recentRevenue    = Subscription::where('created_at', '>=', now()->subDays(30))->sum('amount_paid');
        $recentEnroll     = Subscription::where('created_at', '>=', now()->subDays(7))->count();
        $topCourses       = Course::withCount(['subscriptions' => fn($q) => $q->where('status', 'active')])
                                ->orderByDesc('subscriptions_count')
                                ->take(5)
                                ->get(['title', 'subscriptions_count', 'price']);

        return [
            'total_students'         => User::role('student')->count(),
            'total_teachers'         => User::role('teacher')->count(),
            'total_courses'          => Course::count(),
            'published_courses'      => Course::where('is_published', true)->count(),
            'total_channels'         => Channel::count(),
            'verified_channels'      => Channel::where('is_verified', true)->count(),
            'active_subscriptions'   => Subscription::where('status', 'active')->count(),
            'total_revenue'          => number_format($totalRevenue, 2),
            'revenue_last_30_days'   => number_format($recentRevenue, 2),
            'enrollments_last_7_days'=> $recentEnroll,
            'total_test_attempts'    => TestAttempt::where('status', 'completed')->count(),
            'new_users_today'        => User::whereDate('created_at', today())->count(),
            'new_users_this_week'    => User::where('created_at', '>=', now()->subDays(7))->count(),
            'top_courses'            => $topCourses->map(fn($c) => "{$c->title} ({$c->subscriptions_count} students)")->implode(', '),
            'data_as_of'             => now()->format('d M Y, H:i'),
        ];
    }

    protected function buildSystemPrompt(array $stats): string
    {
        $base = config('ai.system_prompts.admin');

        $context  = "\n\n=== LIVE PLATFORM DATA (as of {$stats['data_as_of']}) ===\n";
        $context .= "👥 Total Students: {$stats['total_students']}\n";
        $context .= "👨‍🏫 Total Teachers: {$stats['total_teachers']}\n";
        $context .= "📚 Total Courses: {$stats['total_courses']} ({$stats['published_courses']} published)\n";
        $context .= "📡 Total Channels: {$stats['total_channels']} ({$stats['verified_channels']} verified)\n";
        $context .= "✅ Active Subscriptions: {$stats['active_subscriptions']}\n";
        $context .= "💰 Total Revenue: ₹{$stats['total_revenue']}\n";
        $context .= "📈 Revenue (Last 30 days): ₹{$stats['revenue_last_30_days']}\n";
        $context .= "📥 New Enrollments (Last 7 days): {$stats['enrollments_last_7_days']}\n";
        $context .= "🆕 New Users Today: {$stats['new_users_today']}\n";
        $context .= "🆕 New Users This Week: {$stats['new_users_this_week']}\n";
        $context .= "📊 Completed Test Attempts: {$stats['total_test_attempts']}\n";
        $context .= "🏆 Top Courses: {$stats['top_courses']}\n";

        $context .= "\n=== INSTRUCTIONS ===\n";
        $context .= "- You have access to the live platform statistics shown above\n";
        $context .= "- Answer admin questions using this real-time data\n";
        $context .= "- Provide insights and trends based on the numbers\n";
        $context .= "- Suggest actionable improvements when relevant\n";
        $context .= "- Be concise and data-driven\n";

        return $base . $context;
    }

    public function getSuggestions(): array
    {
        return [
            '📊 Show me today\'s platform overview',
            '💰 What is the total revenue this month?',
            '👥 How many students registered this week?',
            '🏆 Which are the most popular courses?',
            '📈 Give me a growth summary',
        ];
    }
}
