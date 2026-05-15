<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    /**
     * Initialize the payment and return the checkout URL or data.
     */
    public function initialize(array $data): array;

    /**
     * Verify the payment from the gateway callback.
     */
    public function verify(array $requestData): bool;

    /**
     * Get the name of the gateway.
     */
    public function getName(): string;

    /**
     * Get test card details for development.
     */
    public function getTestCards(): array;
}
