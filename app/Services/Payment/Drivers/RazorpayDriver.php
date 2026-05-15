<?php

namespace App\Services\Payment\Drivers;

use App\Services\Payment\PaymentGatewayInterface;
use Razorpay\Api\Api;

class RazorpayDriver implements PaymentGatewayInterface
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    }

    public function initialize(array $data): array
    {
        $order = $this->api->order->create([
            'receipt' => (string) $data['payment_id'],
            'amount' => (int) round($data['amount'] * 100), // Razorpay uses paise
            'currency' => $data['currency'] ?? 'INR',
            'notes' => [
                'plan_id' => (string) $data['plan_id'],
                'payment_id' => (string) $data['payment_id'],
            ]
        ]);

        return [
            'order_id' => $order['id'],
            'amount' => $order['amount'],
            'currency' => $order['currency'],
            'checkout_url' => null, // Razorpay usually uses a popup, but we can store the order_id
        ];
    }

    public function verify(array $requestData): bool
    {
        $razorpayPaymentId = $requestData['razorpay_payment_id'];
        $razorpayOrderId = $requestData['razorpay_order_id'];
        $razorpaySignature = $requestData['razorpay_signature'];

        try {
            $this->api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getName(): string
    {
        return 'Razorpay';
    }

    public function getTestCards(): array
    {
        return [
            'description' => 'Use any standard Razorpay test card.',
            'number' => '4111 1111 1111 1111',
        ];
    }
}
