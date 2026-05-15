<?php

namespace App\Services\Payment\Drivers;

use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;

class PaystackDriver implements PaymentGatewayInterface
{
    protected string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret');
    }

    public function initialize(array $data): array
    {
        $response = Http::withToken($this->secretKey)
            ->post('https://api.paystack.co/transaction/initialize', [
                'amount' => $data['amount'] * 100, // Paystack uses kobo
                'email' => $data['email'],
                'callback_url' => $data['success_url'],
                'metadata' => [
                    'payment_id' => $data['payment_id'],
                ],
            ]);

        $responseData = $response->json();

        return [
            'checkout_url' => $responseData['data']['authorization_url'],
            'gateway_reference' => $responseData['data']['reference'],
        ];
    }

    public function verify(array $requestData): bool
    {
        $reference = $requestData['reference'] ?? null;
        if (!$reference) return false;

        $response = Http::withToken($this->secretKey)
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        $responseData = $response->json();
        return $responseData['data']['status'] === 'success';
    }

    public function getName(): string
    {
        return 'Paystack';
    }

    public function getTestCards(): array
    {
        return [
            'description' => 'Use Paystack test cards.',
            'number' => '4000 0000 0000 0001',
        ];
    }
}
