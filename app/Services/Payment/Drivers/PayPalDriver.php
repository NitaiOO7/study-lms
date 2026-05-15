<?php

namespace App\Services\Payment\Drivers;

use App\Services\Payment\PaymentGatewayInterface;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalDriver implements PaymentGatewayInterface
{
    protected $provider;

    public function __construct()
    {
        $this->provider = new PayPalClient;
        $this->provider->setApiCredentials(config('paypal'));
        $this->provider->getAccessToken();
    }

    public function initialize(array $data): array
    {
        $order = $this->provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => $data['currency'] ?? 'USD',
                        "value" => $data['amount']
                    ],
                    "description" => $data['plan_name']
                ]
            ],
            "application_context" => [
                "cancel_url" => $data['cancel_url'],
                "return_url" => $data['success_url']
            ]
        ]);

        $checkoutUrl = collect($order['links'])->where('rel', 'approve')->first()['href'];

        return [
            'checkout_url' => $checkoutUrl,
            'gateway_reference' => $order['id'],
        ];
    }

    public function verify(array $requestData): bool
    {
        $token = $requestData['token'] ?? null;
        if (!$token) return false;

        $response = $this->provider->capturePaymentOrder($token);
        return isset($response['status']) && $response['status'] == 'COMPLETED';
    }

    public function getName(): string
    {
        return 'PayPal';
    }

    public function getTestCards(): array
    {
        return [
            'description' => 'Use a PayPal Sandbox Personal Account.',
        ];
    }
}
