<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\AiChatSession;
use Illuminate\Support\Str;

/**
 * AssistantRouterService
 *
 * Routes incoming chat messages to the correct role-specific assistant.
 * Also manages session creation and message persistence.
 */
class AssistantRouterService
{
    public function __construct(
        protected StudentAssistantService   $studentAssistant,
        protected TeacherAssistantService   $teacherAssistant,
        protected AdminAssistantService     $adminAssistant,
        protected DeveloperAssistantService $developerAssistant,
        protected GroqService               $groq
    ) {}

    /**
     * Process a user message and return the AI response.
     *
     * @param  string  $message
     * @param  User    $user
     * @param  string|null  $sessionToken  Pass existing token to continue a session
     * @return array{response: string, session_token: string, session_id: int, model: string|null}
     */
    public function handle(string $message, User $user, ?string $sessionToken = null, ?string $preferredModel = null): array
    {
        $requestedModel = (!$preferredModel || $preferredModel === 'auto') ? null : $preferredModel;
        $useFallbackModels = $requestedModel === null;
        $this->groq->preferModel($requestedModel, $useFallbackModels);

        // Determine user role
        $role = $this->detectRole($user);

        // Get or create chat session
        $session = $this->getOrCreateSession($user, $role, $sessionToken);

        // Auto-generate session title from first message
        if ($session->wasRecentlyCreated || !$session->title) {
            $session->update([
                'title' => Str::limit(strip_tags($message), 60),
            ]);
        }

        // Save user message
        $session->messages()->create([
            'role'    => 'user',
            'content' => $message,
        ]);

        // Check if API is configured
        if (!$this->groq->isConfigured()) {
            $errorMsg = $this->getUnconfiguredMessage();
            $session->messages()->create([
                'role'    => 'assistant',
                'content' => $errorMsg,
            ]);
            return [
                'response'      => $errorMsg,
                'session_token' => $session->session_token,
                'session_id'    => $session->id,
                'model'         => null,
            ];
        }

        // Route to correct assistant
        try {
            $response = match ($role) {
                'teacher'   => $this->teacherAssistant->respond($message, $session, $user),
                'admin'     => $this->adminAssistant->respond($message, $session, $user),
                'developer' => $this->developerAssistant->respond($message, $session, $user),
                default     => $this->studentAssistant->respond($message, $session, $user),
            };
        } catch (\RuntimeException $e) {
            $response = "⚠️ I'm having trouble connecting to the AI service right now. Please try again in a moment.\n\n**Error:** " . $e->getMessage();
        }

        // Save assistant response
        $session->messages()->create([
            'role'    => 'assistant',
            'content' => $response,
            'model'   => $this->groq->getLastModel() ?? $requestedModel,
        ]);

        return [
            'response'      => $response,
            'session_token' => $session->session_token,
            'session_id'    => $session->id,
            'model'         => $this->groq->getLastModel() ?? $requestedModel,
        ];
    }

    /**
     * Get suggested questions for the current user's role.
     */
    public function getSuggestions(User $user): array
    {
        $role = $this->detectRole($user);

        return match ($role) {
            'teacher'   => $this->teacherAssistant->getSuggestions(),
            'admin'     => $this->adminAssistant->getSuggestions(),
            'developer' => $this->developerAssistant->getSuggestions(),
            default     => $this->studentAssistant->getSuggestions(),
        };
    }

    public function getConfiguredModels(): array
    {
        return $this->groq->getConfiguredModels();
    }

    /**
     * Detect the user's primary role.
     */
    protected function detectRole(User $user): string
    {
        if ($user->hasRole('admin'))   return 'admin';
        if ($user->hasRole('teacher')) return 'teacher';
        return 'student';
    }

    /**
     * Get or create a chat session.
     */
    protected function getOrCreateSession(User $user, string $role, ?string $token): AiChatSession
    {
        if ($token) {
            $session = AiChatSession::where('session_token', $token)
                ->where('user_id', $user->id)
                ->first();

            if ($session) {
                return $session;
            }
        }

        return AiChatSession::create([
            'user_id' => $user->id,
            'role'    => $role,
        ]);
    }

    protected function getUnconfiguredMessage(): string
    {
        return "⚠️ **AI Assistant Not Configured Yet**\n\nThe Groq API key has not been set up yet.\n\nTo enable the AI assistant:\n1. Sign up for free at [console.groq.com](https://console.groq.com)\n2. Generate an API key\n3. Add to your `.env` file:\n```\nGROQ_API_KEY=gsk_your_key_here\n```\n4. Restart the server\n\nThe assistant will be ready to help immediately after configuration!";
    }
}
