<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\AiChatSession;

/**
 * TeacherAssistantService
 *
 * Handles AI responses for the Teacher role.
 * Provides guidance on course creation, content management,
 * student management, earnings, and platform features.
 */
class TeacherAssistantService
{
    public function __construct(
        protected GroqService            $groq,
        protected KnowledgeSearchService $search
    ) {}

    public function respond(string $message, AiChatSession $session, User $user): string
    {
        // Search relevant FAQs for teachers
        $faqs   = $this->search->searchFaqs($message, 'teacher', 3);
        $faqCtx = $this->search->formatFaqsAsContext($faqs);

        $systemPrompt = $this->buildSystemPrompt($user, $faqCtx);

        $messages = array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $session->getApiHistory(config('ai.history_limit', 10)),
            [['role' => 'user', 'content' => $message]]
        );

        $result = $this->groq->chat($messages);
        return $result['content'];
    }

    protected function buildSystemPrompt(User $user, string $faqContext): string
    {
        $base    = config('ai.system_prompts.teacher');
        $channel = $user->channel;

        $context = "\n\n=== TEACHER CONTEXT ===\n";
        $context .= "Teacher Name: {$user->name}\n";

        if ($channel) {
            $courseCount    = $channel->courses()->count();
            $materialCount  = $channel->studyMaterials()->count();
            $testSeriesCount = $channel->testSeries()->count();
            $revenue        = \App\Models\Subscription::whereIn('course_id', $channel->courses()->pluck('id'))->sum('amount_paid');

            $context .= "Channel: {$channel->name}\n";
            $context .= "Total Courses: {$courseCount}\n";
            $context .= "Study Materials: {$materialCount}\n";
            $context .= "Test Series: {$testSeriesCount}\n";
            $context .= "Total Revenue: ₹{$revenue}\n";
            $context .= "Channel Verified: " . ($channel->is_verified ? 'Yes' : 'Pending') . "\n";
        } else {
            $context .= "Channel: Not created yet\n";
        }

        $context .= "\n=== LMS TEACHER FEATURES ===\n";
        $context .= "- Create and publish courses with video/PDF lessons\n";
        $context .= "- Upload study materials (PDF, video, documents)\n";
        $context .= "- Create test series with MCQ, MSQ, and NAT questions\n";
        $context .= "- Create course bundles with other teachers\n";
        $context .= "- View student enrollments and revenue\n";
        $context .= "- Manage channel subscription plans\n";

        if (!empty($faqContext)) {
            $context .= "\n" . $faqContext;
        }

        $context .= "\n\n=== INSTRUCTIONS ===\n";
        $context .= "- Be professional and instructive\n";
        $context .= "- Provide actionable step-by-step guidance\n";
        $context .= "- Reference the teacher's actual stats when relevant\n";
        $context .= "- Help teachers grow their channel and increase revenue\n";

        return $base . $context;
    }

    public function getSuggestions(): array
    {
        return [
            '📖 How do I create a new course?',
            '🎬 How do I upload course videos?',
            '📝 How do I create a test series?',
            '💰 How do I view my earnings?',
            '📦 How do I create a course bundle?',
        ];
    }
}
