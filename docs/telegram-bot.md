# Telegram Bot Setup

## 1. Create a bot

1. Open Telegram and message `@BotFather`.
2. Run `/newbot`.
3. Copy the bot token.

## 2. Configure Laravel

Add these values to `.env`:

```env
TELEGRAM_BOT_TOKEN=your_botfather_token
TELEGRAM_WEBHOOK_URL=https://your-domain.com/telegram/webhook
TELEGRAM_ALLOWED_CHAT_IDS=123456789
TELEGRAM_REPORT_LIMIT=10
```

`TELEGRAM_ALLOWED_CHAT_IDS` is optional, but recommended for admin reports. Use a comma-separated list for multiple admins.

## 3. Register the webhook

Telegram requires a public HTTPS URL. For production:

```bash
php artisan telegram:set-webhook
```

Check the current status any time:

```bash
php artisan telegram:health
php artisan telegram:webhook-info
```

If the health command shows `http://localhost`, Telegram will not send messages to Laravel.

Or pass a URL directly:

```bash
php artisan telegram:set-webhook https://your-domain.com/telegram/webhook
```

For local testing, expose Laravel with a tunnel such as ngrok, then set:

```env
APP_URL=https://your-ngrok-url.ngrok-free.app
TELEGRAM_WEBHOOK_URL="${APP_URL}/telegram/webhook"
```

Then run:

```bash
php artisan config:clear
php artisan telegram:set-webhook
```

## Local Testing Without HTTPS

When your app is only running on `http://localhost`, use polling instead of webhook:

```bash
php artisan telegram:delete-webhook
php artisan telegram:poll
```

Keep that terminal open, then send `hi` or `/start` to the bot. The bot will reply from your local Laravel app.

## 4. Bot Commands

Send `hi` or `/start` to open the menu.

Available buttons:

- `students`
- `teachers`
- `purchases`
- `new students`
- `new teachers`
- `revenue`

The bot reads from the MySQL-backed Laravel tables: `users`, Spatie role tables, and `payments`.
