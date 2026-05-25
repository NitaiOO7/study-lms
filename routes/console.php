<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\Telegram\TelegramBotService;
use Telegram\Bot\Api;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('telegram:set-webhook {url?}', function (?string $url = null) {
    $webhookUrl = $url ?: config('telegram.webhook_url');

    if (!$webhookUrl) {
        $this->error('Set TELEGRAM_WEBHOOK_URL in .env or pass the URL as an argument.');
        return 1;
    }

    try {
        app(TelegramBotService::class)->setWebhook($webhookUrl);
    } catch (\Throwable $exception) {
        $this->error('Could not set Telegram webhook: '.$exception->getMessage());
        $this->warn('Telegram requires a public HTTPS webhook URL.');
        return 1;
    }

    $this->info("Telegram webhook set: {$webhookUrl}");
    return 0;
})->purpose('Register the LMS Telegram bot webhook URL');

Artisan::command('telegram:webhook-info', function () {
    try {
        $telegram = new Api((string) config('telegram.bot_token'));
        $info = $telegram->getWebhookInfo();

        $this->line(json_encode($info->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return 0;
    } catch (\Throwable $exception) {
        $this->error('Could not fetch Telegram webhook info: '.$exception->getMessage());
        return 1;
    }
})->purpose('Show Telegram webhook status and last delivery error');

Artisan::command('telegram:health', function () {
    $webhookUrl = (string) config('telegram.webhook_url');

    $this->line('Token configured: '.(filled(config('telegram.bot_token')) ? 'yes' : 'no'));
    $this->line('Configured webhook URL: '.($webhookUrl ?: 'not set'));

    if (!$webhookUrl) {
        $this->error('TELEGRAM_WEBHOOK_URL is missing.');
    } elseif (!str_starts_with($webhookUrl, 'https://')) {
        $this->error('Telegram webhooks require HTTPS. Current URL is not valid for webhook delivery.');
    }

    try {
        $telegram = new Api((string) config('telegram.bot_token'));
        $bot = $telegram->getMe()->toArray();
        $info = $telegram->getWebhookInfo()->toArray();

        $this->line('Bot username: @'.($bot['username'] ?? 'unknown'));
        $this->line('Telegram webhook URL: '.($info['url'] ?: 'not set'));
        $this->line('Pending updates: '.($info['pending_update_count'] ?? 0));

        if (!empty($info['last_error_message'])) {
            $this->warn('Last Telegram error: '.$info['last_error_message']);
        }

        return 0;
    } catch (\Throwable $exception) {
        $this->error('Telegram API check failed: '.$exception->getMessage());
        return 1;
    }
})->purpose('Check Telegram bot token, configured webhook URL, and Telegram delivery status');

Artisan::command('telegram:delete-webhook', function () {
    try {
        $telegram = new Api((string) config('telegram.bot_token'));
        $telegram->deleteWebhook();

        $this->info('Telegram webhook deleted. You can now use telegram:poll for local testing.');
        return 0;
    } catch (\Throwable $exception) {
        $this->error('Could not delete Telegram webhook: '.$exception->getMessage());
        return 1;
    }
})->purpose('Delete Telegram webhook so local polling can receive updates');

Artisan::command('telegram:poll {--once : Process pending updates once and exit}', function () {
    try {
        $telegram = new Api((string) config('telegram.bot_token'));
        $bot = app(TelegramBotService::class);
        $offset = null;

        $this->info('Polling Telegram updates. Press Ctrl+C to stop.');

        do {
            $updates = $telegram->getUpdates(array_filter([
                'offset' => $offset,
                'timeout' => 25,
                'allowed_updates' => ['message', 'edited_message'],
            ]), false);

            foreach ($updates as $update) {
                $payload = $update->toArray();
                $offset = ($payload['update_id'] ?? 0) + 1;
                $bot->handleWebhook($payload);
                $this->line('Processed update '.$payload['update_id']);
            }

            if ($this->option('once')) {
                break;
            }
        } while (true);

        return 0;
    } catch (\Throwable $exception) {
        $this->error('Telegram polling failed: '.$exception->getMessage());
        return 1;
    }
})->purpose('Poll Telegram updates locally without a public webhook');
