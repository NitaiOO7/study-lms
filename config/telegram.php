<?php

return [
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),

    'webhook_url' => env('TELEGRAM_WEBHOOK_URL'),

    /*
    |--------------------------------------------------------------------------
    | Allowed Chat IDs
    |--------------------------------------------------------------------------
    |
    | Add comma-separated Telegram chat IDs to restrict LMS reports to admins.
    | Leave empty during local testing if you want the bot to respond to anyone.
    |
    */
    'allowed_chat_ids' => array_filter(
        array_map('trim', explode(',', (string) env('TELEGRAM_ALLOWED_CHAT_IDS', '')))
    ),

    'default_limit' => (int) env('TELEGRAM_REPORT_LIMIT', 10),
];
