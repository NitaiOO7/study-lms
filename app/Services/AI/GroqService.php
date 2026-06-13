<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GroqService — Low-level wrapper around the Groq API.
 *
 * Groq uses the OpenAI-compatible API format, so this can also be used
 * with OpenAI by swapping the base_url and api_key in config/ai.php.
 */
class GroqService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $model;
    protected array  $fallbackModels;
    protected bool   $useFallbackModels = true;
    protected ?string $lastModel = null;
    protected int    $timeout;

    public function __construct()
    {
        $provider      = config('ai.provider', 'groq');
        $this->apiKey  = config("ai.{$provider}.api_key");
        $this->baseUrl = config("ai.{$provider}.base_url");
        $this->model   = config("ai.{$provider}.model");
        $this->fallbackModels = config("ai.{$provider}.fallback_models", []);
        $this->timeout = config("ai.{$provider}.timeout", 60);
    }

    /**
     * Send a chat completion request to Groq.
     *
     * @param  array  $messages   Array of ['role' => ..., 'content' => ...] objects
     * @param  int    $maxTokens  Maximum tokens in response
     * @param  float  $temperature
     * @return array{content: string, tokens_used: int, model: string}
     */
    public function chat(array $messages, int $maxTokens = null, float $temperature = null): array
    {
        $maxTokens   ??= config('ai.max_tokens', 2048);
        $temperature ??= config('ai.temperature', 0.7);

        $models = $this->modelsToTry();
        $lastError = null;

        foreach ($models as $model) {
            try {
                $response = Http::withToken($this->apiKey)
                    ->timeout($this->timeout)
                    ->post("{$this->baseUrl}/chat/completions", [
                        'model'       => $model,
                        'messages'    => $messages,
                        'max_tokens'  => $maxTokens,
                        'temperature' => $temperature,
                    ]);
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error('Groq connection failed', [
                    'error' => $e->getMessage(),
                    'model' => $model,
                ]);

                throw new \RuntimeException('Could not connect to AI service. Please try again shortly.');
            }

            if ($response->failed()) {
                $lastError = $this->formatApiError($response->status(), $response->body(), $model);
                Log::error('Groq API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'model'  => $model,
                ]);

                if ($this->shouldTryNextModel($response->status(), $response->body())) {
                    continue;
                }

                throw new \RuntimeException($lastError);
            }

            $data = $response->json();
            $this->lastModel = $data['model'] ?? $model;

            return [
                'content'     => $data['choices'][0]['message']['content'] ?? 'No response generated.',
                'tokens_used' => $data['usage']['total_tokens'] ?? 0,
                'model'       => $this->lastModel,
            ];
        }

        throw new \RuntimeException($lastError ?? 'No configured Groq model is currently available.');
    }

    public function preferModel(?string $model, bool $useFallbackModels = true): void
    {
        $this->useFallbackModels = $useFallbackModels;

        if (!$model) {
            return;
        }

        if (in_array($model, $this->modelsToTry(), true)) {
            $this->model = $model;
        }
    }

    public function getConfiguredModels(): array
    {
        return $this->modelsToTry();
    }

    protected function modelsToTry(): array
    {
        if (!$this->useFallbackModels) {
            return [$this->model];
        }

        return collect(array_merge([$this->model], $this->fallbackModels))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function shouldTryNextModel(int $status, string $body): bool
    {
        if (in_array($status, [400, 404, 429, 503], true)) {
            return true;
        }

        $lowerBody = strtolower($body);

        return str_contains($lowerBody, 'model')
            && (
                str_contains($lowerBody, 'decommissioned')
                || str_contains($lowerBody, 'deprecated')
                || str_contains($lowerBody, 'not found')
                || str_contains($lowerBody, 'not available')
                || str_contains($lowerBody, 'does not exist')
                || str_contains($lowerBody, 'rate limit')
            );
    }

    protected function formatApiError(int $status, string $body, string $model): string
    {
        $message = data_get(json_decode($body, true), 'error.message');

        return "AI service error: {$status} while using model `{$model}`"
            . ($message ? ". {$message}" : '.');
    }

    /**
     * Simple single-turn completion (no history).
     */
    public function complete(string $systemPrompt, string $userMessage): string
    {
        $result = $this->chat([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user',   'content' => $userMessage],
        ]);

        return $result['content'];
    }

    public function getLastModel(): ?string
    {
        return $this->lastModel;
    }

    /**
     * Check if the API key is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && str_starts_with($this->apiKey, 'gsk_');
    }
}
