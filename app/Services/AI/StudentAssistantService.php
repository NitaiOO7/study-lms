<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\AiChatSession;

/**
 * StudentAssistantService
 *
 * Handles AI responses for the Student role.
 * Searches the FAQ knowledge base and provides step-by-step
 * guidance for all common student LMS workflows.
 */
class StudentAssistantService
{
    public function __construct(
        protected GroqService          $groq,
        protected KnowledgeSearchService $search
    ) {}

    /**
     * Generate a response for a student message.
     */
    public function respond(string $message, AiChatSession $session, User $user): string
    {
        // Search relevant FAQs
        $faqs    = $this->search->searchFaqs($message, 'student', 3);
        $faqCtx  = $this->search->formatFaqsAsContext($faqs);

        // Build the system prompt with student context
        $systemPrompt = $this->buildSystemPrompt($user, $faqCtx);

        // Build message history
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
        $base = config('ai.system_prompts.student');

        $context = "\n\n=== STUDENT CONTEXT ===\n";
        $context .= "Student Name: {$user->name}\n";
        $context .= "Email: {$user->email}\n";

        // Add enrolled course count
        $enrolledCount = $user->subscriptions()->where('status', 'active')->count();
        $context .= "Active Enrollments: {$enrolledCount} courses\n";

        $context .= "\n=== LMS PLATFORM INFORMATION ===\n";
        $context .= "Platform: EduVerse LMS\n";
        $context .= "Features: Online courses, Video lessons, PDF notes, Test series, Certificates, Community forum\n";
        $context .= "Payment: Razorpay, Stripe, PayPal\n";

        if (!empty($faqContext)) {
            $context .= "\n" . $faqContext;
        }

        $context .= "\n\n=== INSTRUCTIONS ===\n";
        $context .= "- Answer in a friendly, encouraging tone\n";
        $context .= "- Provide numbered step-by-step guides for procedural questions\n";
        $context .= "- If the student asks about something not in the FAQ, use your knowledge of typical LMS platforms\n";
        $context .= "- Keep answers concise but complete\n";
        $context .= "- Use emojis sparingly to make responses friendly\n";

        return $base . $context;
    }

    /**
     * Get suggested quick questions for students
     */
    public function getSuggestions(): array
    {
        return [
            '📚 How do I purchase a course?',
            '🎓 How do I download my certificate?',
            '🔑 How do I reset my password?',
            '▶️ How do I watch course videos?',
            '🎟️ How do I apply a coupon code?',
        ];
    }
}
