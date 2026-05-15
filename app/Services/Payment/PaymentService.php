<?php

namespace App\Services\Payment;

use App\Services\Payment\Drivers\StripeDriver;
use App\Services\Payment\Drivers\PayPalDriver;
use App\Services\Payment\Drivers\RazorpayDriver;
use App\Services\Payment\Drivers\PaystackDriver;
use InvalidArgumentException;

class PaymentService
{
    protected array $drivers = [
        'stripe' => StripeDriver::class,
        'paypal' => PayPalDriver::class,
        'razorpay' => RazorpayDriver::class,
        'paystack' => PaystackDriver::class,
    ];

    /**
     * Resolve the payment gateway driver.
     */
    public function driver(string $gateway): PaymentGatewayInterface
    {
        if (!isset($this->drivers[$gateway])) {
            throw new InvalidArgumentException("Payment gateway [{$gateway}] is not supported.");
        }

        $driverClass = $this->drivers[$gateway];
        return new $driverClass();
    }

    /**
     * Get all available gateways.
     */
    public function getAvailableGateways(): array
    {
        $gateways = [];
        foreach ($this->drivers as $name => $class) {
            $driver = new $class();
            $gateways[$name] = [
                'name' => $driver->getName(),
                'test_cards' => $driver->getTestCards(),
            ];
        }
        return $gateways;
    }
}
