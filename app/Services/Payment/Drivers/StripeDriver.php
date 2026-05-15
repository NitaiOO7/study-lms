<?php

namespace App\Services\Payment\Drivers;

use App\Services\Payment\PaymentGatewayInterface;
use Stripe\StripeClient;

class StripeDriver implements PaymentGatewayInterface
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function initialize(array $data): array
    {
        $session = $this->stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $data['currency'] ?? 'usd',
                    'product_data' => [
                        'name' => $data['plan_name'],
                    ],
                    'unit_amount' => $data['amount'] * 100, // Stripe uses cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $data['success_url'] . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $data['cancel_url'],
            'metadata' => [
                'payment_id' => $data['payment_id'],
                'plan_id' => $data['plan_id'],
            ],
        ]);

        return [
            'checkout_url' => $session->url,
            'gateway_reference' => $session->id,
        ];
    }

    public function verify(array $requestData): bool
    {
        $sessionId = $requestData['session_id'] ?? null;
        if (!$sessionId) return false;

        $session = $this->stripe->checkout->sessions->retrieve($sessionId);
        return $session->payment_status === 'paid';
    }

    public function getName(): string
    {
        return 'Stripe';
    }

    public function getTestCards(): array
    {
        return [
            'number' => '4242 4242 4242 4242',
            'expiry' => '12/26',
            'cvc' => '123',
        ];
    }
}
