<?php

namespace App\Http\Controllers;

use App\Models\AiChatSession;
use App\Services\AI\AssistantRouterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiAssistantController extends Controller
{
    public function __construct(
        protected AssistantRouterService $assistant
    ) {}

    public function index(Request $request): View
    {
        return view('assistant.index', [
            'suggestions' => $this->assistant->getSuggestions($request->user()),
            'models' => $this->assistant->getConfiguredModels(),
        ]);
    }

    public function message(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:4000'],
            'session_token' => ['nullable', 'string', 'max:64'],
            'preferred_model' => ['nullable', 'string', 'max:120'],
        ]);

        $result = $this->assistant->handle(
            $validated['message'],
            $request->user(),
            $validated['session_token'] ?? null,
            $validated['preferred_model'] ?? null
        );

        return response()->json($result);
    }

    public function sessions(Request $request): JsonResponse
    {
        $sessions = AiChatSession::query()
            ->where('user_id', $request->user()->id)
            ->with(['messages' => fn($query) => $query->latest('created_at')->limit(1)])
            ->latest('updated_at')
            ->limit(30)
            ->get()
            ->map(fn(AiChatSession $session) => [
                'token' => $session->session_token,
                'title' => $session->title ?: 'Untitled chat',
                'role' => $session->role,
                'updated_at' => optional($session->updated_at)->diffForHumans(),
                'last_message' => optional($session->messages->first())->content,
            ]);

        return response()->json(['sessions' => $sessions]);
    }

    public function showSession(Request $request, string $sessionToken): JsonResponse
    {
        $session = AiChatSession::query()
            ->where('user_id', $request->user()->id)
            ->where('session_token', $sessionToken)
            ->with(['messages' => fn($query) => $query->oldest('created_at')])
            ->firstOrFail();

        return response()->json([
            'session' => [
                'token' => $session->session_token,
                'title' => $session->title ?: 'Untitled chat',
                'role' => $session->role,
                'model' => optional($session->messages->where('role', 'assistant')->last())->model,
            ],
            'messages' => $session->messages->map(fn($message) => [
                'role' => $message->role,
                'content' => $message->content,
                'model' => $message->model,
                'created_at' => optional($message->created_at)->toDateTimeString(),
            ])->values(),
        ]);
    }
}
