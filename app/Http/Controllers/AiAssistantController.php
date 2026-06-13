<?php

namespace App\Http\Controllers;

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
}
