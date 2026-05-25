<?php

namespace App\Http\Controllers;

use App\Services\Telegram\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramWebhookController extends Controller
{
    public function __invoke(Request $request, TelegramBotService $bot): JsonResponse
    {
        try {
            $bot->handleWebhook($request->all());
        } catch (Throwable $exception) {
            Log::error('Telegram webhook failed', [
                'payload' => $request->all(),
                'exception' => $exception,
            ]);
        }

        return response()->json(['ok' => true]);
    }
}
