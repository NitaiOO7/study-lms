<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\AiChatSession;

/**
 * DeveloperAssistantService
 *
 * Handles AI responses for the Developer role.
 * Uses full RAG pipeline:
 *   1. Search indexed codebase chunks by query
 *   2. Inject relevant code as context to the LLM
 *   3. LLM generates grounded answers about the actual codebase
 *
 * Can explain existing code AND generate new Laravel features
 * (migrations, models, controllers, services) based on the project structure.
 */
class DeveloperAssistantService
{
    public function __construct(
        protected GroqService            $groq,
        protected KnowledgeSearchService $search,
        protected CodeIndexerService     $indexer
    ) {}

    public function respond(string $message, AiChatSession $session, User $user): string
    {
        $topK = config('ai.context_chunks', 5);

        // RAG: Search relevant code chunks
        $codeChunks  = $this->search->searchChunks($message, null, $topK);
        $codeContext = $this->search->formatChunksAsContext($codeChunks);

        // Also search route-type and schema chunks
        $routeChunks  = $this->search->searchChunks($message, 'route', 2);
        $schemaChunks = $this->search->searchChunks($message, 'schema', 2);

        $extraContext = '';
        if ($routeChunks->isNotEmpty()) {
            $extraContext .= $this->search->formatChunksAsContext($routeChunks);
        }
        if ($schemaChunks->isNotEmpty()) {
            $extraContext .= $this->search->formatChunksAsContext($schemaChunks);
        }

        $indexStats   = $this->indexer->getStats();
        $systemPrompt = $this->buildSystemPrompt($codeContext . $extraContext, $indexStats);

        $messages = array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $session->getApiHistory(config('ai.history_limit', 10)),
            [['role' => 'user', 'content' => $message]]
        );

        $result = $this->groq->chat($messages, 4096);
        return $result['content'];
    }

    protected function buildSystemPrompt(string $codeContext, array $indexStats): string
    {
        $base = config('ai.system_prompts.developer');

        $context  = "\n\n=== PROJECT INFORMATION ===\n";
        $context .= "Framework: Laravel 12\n";
        $context .= "Auth: Laravel Breeze + Spatie Permission (roles: admin, teacher, student)\n";
        $context .= "Payments: Stripe, Razorpay, PayPal (via srmklive/paypal)\n";
        $context .= "Notifications: Telegram Bot SDK\n";
        $context .= "Frontend: Blade + Vite + Tailwind CSS\n";
        $context .= "Queue: Database driver\n";
        $context .= "DB: MySQL\n\n";

        $context .= "=== KEY ARCHITECTURE ===\n";
        $context .= "- Controllers: app/Http/Controllers/ (Admin, Auth, Teacher, Student, Checkout, Community)\n";
        $context .= "- Models: app/Models/ (User, Course, Channel, Subscription, Lesson, TestSeries, Section, Question, etc.)\n";
        $context .= "- Services: app/Services/ (AnalyticsService, Payment/, Telegram/)\n";
        $context .= "- Routes: routes/web.php (admin.*, teacher.*, student.*, community.*)\n";
        $context .= "- Migrations: database/migrations/ (28 migrations)\n";
        $context .= "- Views: resources/views/ (admin, teacher, student, community, layouts, components)\n\n";

        $context .= "=== CODEBASE INDEX STATUS ===\n";
        $context .= "Total Indexed Chunks: {$indexStats['total_chunks']}\n";
        $context .= "Total Indexed Files: {$indexStats['total_files']}\n";
        if (!empty($indexStats['by_type'])) {
            foreach ($indexStats['by_type'] as $type => $count) {
                $context .= "  - {$type}: {$count} chunks\n";
            }
        }
        if ($indexStats['total_chunks'] === 0) {
            $context .= "\n⚠️ IMPORTANT: Codebase is not yet indexed! Run: php artisan ai:index-codebase\n";
        }

        if (!empty($codeContext)) {
            $context .= "\n" . $codeContext;
        }

        $context .= "\n\n=== INSTRUCTIONS ===\n";
        $context .= "- Always reference actual file paths from the codebase context above\n";
        $context .= "- When explaining code, quote the relevant sections from the context\n";
        $context .= "- When generating new code, follow the existing patterns in the codebase\n";
        $context .= "- Use PSR-12 coding standards consistent with the existing code\n";
        $context .= "- For feature implementation requests, provide: Migration + Model + Controller + Routes + Blade view\n";
        $context .= "- If the codebase context doesn't have enough info, say so and ask for clarification\n";

        return $base . $context;
    }

    public function getSuggestions(): array
    {
        return [
            '🔍 How is user login implemented?',
            '💳 Where is the payment/checkout logic?',
            '📁 Show me the course subscription flow',
            '⚙️ Implement a teacher coupon system',
            '🗺️ List all available API routes',
        ];
    }
}
