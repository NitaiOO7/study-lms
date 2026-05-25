<?php

namespace App\Services\Telegram;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;
use Throwable;

class TelegramBotService
{
    private ?Api $telegram = null;

    public function handleWebhook(array $payload): void
    {
        $message = $payload['message'] ?? $payload['edited_message'] ?? null;

        if (!$message || !isset($message['chat']['id'])) {
            return;
        }

        $chatId = (string) $message['chat']['id'];
        $text = $this->normalizeCommand($message['text'] ?? '');

        if (!$this->isAllowedChat($chatId)) {
            $this->sendMessage($chatId, "🔒 <b>Access denied</b>\nThis Telegram chat is not allowed to view LMS reports.");
            return;
        }

        try {
            match ($text) {
                '/start', 'start', 'hi', 'hello', 'menu' => $this->sendMenu($chatId),
                'students' => $this->sendLatestUsers($chatId, 'student'),
                'teachers' => $this->sendLatestUsers($chatId, 'teacher'),
                'new students' => $this->sendNewUsers($chatId, 'student'),
                'new teachers' => $this->sendNewUsers($chatId, 'teacher'),
                'purchases', 'orders' => $this->sendLatestPurchases($chatId),
                'revenue' => $this->sendRevenue($chatId),
                default => $this->sendUnknownCommand($chatId),
            };
        } catch (Throwable $exception) {
            Log::error('Telegram bot command failed', [
                'chat_id' => $chatId,
                'text' => $text,
                'exception' => $exception,
            ]);

            $this->sendMessage($chatId, "⚠️ <b>Something went wrong.</b>\nPlease try again in a moment.");
        }
    }

    public function setWebhook(?string $url = null): bool
    {
        return $this->telegram()->setWebhook([
            'url' => $url ?: (string) config('telegram.webhook_url'),
            'allowed_updates' => ['message', 'edited_message'],
        ]);
    }

    public function sendMenu(string $chatId): void
    {
        $text = implode("\n", [
            "🎓 <b>LMS Admin Bot</b>",
            '',
            'Choose a report from the keyboard below:',
            '',
            '👨‍🎓 students - latest students',
            '👨‍🏫 teachers - latest teachers',
            '🧾 purchases - latest orders',
            '🆕 new students - today\'s students',
            '🆕 new teachers - today\'s teachers',
            '💰 revenue - total revenue',
        ]);

        $this->sendMessage($chatId, $text, $this->menuKeyboard());
    }

    private function sendLatestUsers(string $chatId, string $role): void
    {
        $users = User::role($role)
            ->latest()
            ->take($this->limit())
            ->get(['id', 'name', 'email', 'phone', 'created_at']);

        $title = $role === 'teacher' ? 'Latest Teachers' : 'Latest Students';
        $icon = $role === 'teacher' ? '👨‍🏫' : '👨‍🎓';

        $this->sendMessage($chatId, $this->formatUsers($users, "{$icon} <b>{$title}</b>"));
    }

    private function sendNewUsers(string $chatId, string $role): void
    {
        $users = User::role($role)
            ->whereDate('created_at', Carbon::today())
            ->latest()
            ->take($this->limit())
            ->get(['id', 'name', 'email', 'phone', 'created_at']);

        $title = $role === 'teacher' ? 'Today\'s New Teachers' : 'Today\'s New Students';
        $icon = $role === 'teacher' ? '🆕👨‍🏫' : '🆕👨‍🎓';

        $this->sendMessage($chatId, $this->formatUsers($users, "{$icon} <b>{$title}</b>"));
    }

    private function sendLatestPurchases(string $chatId): void
    {
        $payments = Payment::with('user:id,name,email')
            ->latest()
            ->take($this->limit())
            ->get();

        if ($payments->isEmpty()) {
            $this->sendMessage($chatId, "🧾 <b>Latest Purchases</b>\n\nNo purchases found yet.");
            return;
        }

        $lines = $payments->map(function (Payment $payment, int $index): string {
            $user = $payment->user;
            $buyer = $user ? $this->escape($user->name) : 'Unknown user';
            $email = $user ? $this->escape($user->email) : 'No email';
            $amount = $this->money((float) $payment->amount, $payment->currency);
            $status = strtoupper($payment->status);
            $date = $payment->created_at?->format('d M Y, h:i A') ?? 'N/A';

            return implode("\n", [
                '<b>'.($index + 1).". {$buyer}</b>",
                "📧 {$email}",
                "💳 {$amount} via ".$this->escape($payment->gateway),
                "📌 {$status} | {$date}",
            ]);
        })->implode("\n\n");

        $this->sendMessage($chatId, "🧾 <b>Latest Purchases</b>\n\n{$lines}");
    }

    private function sendRevenue(string $chatId): void
    {
        $totals = Payment::query()
            ->where('status', 'success')
            ->selectRaw('currency, SUM(amount) as total, COUNT(*) as orders_count')
            ->groupBy('currency')
            ->orderBy('currency')
            ->get();

        if ($totals->isEmpty()) {
            $this->sendMessage($chatId, "💰 <b>Total Revenue</b>\n\nNo successful revenue found yet.");
            return;
        }

        $lines = $totals->map(function ($row): string {
            return '💵 '.$this->money((float) $row->total, $row->currency).' from '.$row->orders_count.' orders';
        })->implode("\n");

        $today = Payment::query()
            ->where('status', 'success')
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        $this->sendMessage($chatId, "💰 <b>Total Revenue</b>\n\n{$lines}\n\n📅 Today: ".$this->money((float) $today));
    }

    private function sendUnknownCommand(string $chatId): void
    {
        $this->sendMessage(
            $chatId,
            "🤖 I did not understand that command.\nType <b>hi</b> to open the LMS menu.",
            $this->menuKeyboard()
        );
    }

    private function sendMessage(string $chatId, string $text, Keyboard|string|null $keyboard = null): void
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];

        if ($keyboard) {
            $params['reply_markup'] = $keyboard;
        }

        $this->telegram()->sendMessage($params);
    }

    private function telegram(): Api
    {
        if ($this->telegram) {
            return $this->telegram;
        }

        $token = (string) config('telegram.bot_token');

        if ($token === '') {
            throw new RuntimeException('TELEGRAM_BOT_TOKEN is not configured.');
        }

        return $this->telegram = new Api($token);
    }

    private function menuKeyboard(): Keyboard
    {
        return Keyboard::make()
            ->setResizeKeyboard(true)
            ->setIsPersistent(true)
            ->row([
                Keyboard::button('students'),
                Keyboard::button('teachers'),
            ])
            ->row([
                Keyboard::button('purchases'),
                Keyboard::button('revenue'),
            ])
            ->row([
                Keyboard::button('new students'),
                Keyboard::button('new teachers'),
            ]);
    }

    private function formatUsers(Collection $users, string $heading): string
    {
        if ($users->isEmpty()) {
            return "{$heading}\n\nNo records found.";
        }

        $lines = $users->map(function (User $user, int $index): string {
            $phone = $user->phone ? "\n📱 ".$this->escape($user->phone) : '';
            $joined = $user->created_at?->format('d M Y, h:i A') ?? 'N/A';

            return implode('', [
                '<b>'.($index + 1).'. '.$this->escape($user->name).'</b>',
                "\n📧 ".$this->escape($user->email),
                $phone,
                "\n🕒 Joined: {$joined}",
            ]);
        })->implode("\n\n");

        return "{$heading}\n\n{$lines}";
    }

    private function isAllowedChat(string $chatId): bool
    {
        $allowedChatIds = config('telegram.allowed_chat_ids', []);

        return empty($allowedChatIds) || in_array($chatId, $allowedChatIds, true);
    }

    private function normalizeCommand(string $text): string
    {
        return trim(mb_strtolower($text));
    }

    private function money(float $amount, string $currency = 'USD'): string
    {
        return strtoupper($currency).' '.number_format($amount, 2);
    }

    private function limit(): int
    {
        return max(1, min(25, (int) config('telegram.default_limit', 10)));
    }

    private function escape(?string $value): string
    {
        return e($value ?? '');
    }
}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                