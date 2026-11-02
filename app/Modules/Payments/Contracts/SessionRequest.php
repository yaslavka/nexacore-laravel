<?php

namespace App\Modules\Payments\Contracts;

/**
 * Data required to open a payment session at a payment gateway.
 * Amount is always in minor units (cents, tyiyn, kopecks…).
 */
final class SessionRequest
{
    public function __construct(
        public readonly string $orderId,
        public readonly int $amount,
        public readonly string $currency,
        public readonly ?string $product = null,
        public readonly ?string $lang = null,
        public readonly ?string $email = null,
        public readonly ?string $customerId = null,
        public readonly ?string $returnUrlSuccess = null,
        public readonly ?string $returnUrlDecline = null,
        public readonly int $lifetimeMinutes = 60,
        public readonly array $metadata = [],
    ) {
    }
}
