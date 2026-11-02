<?php

namespace App\Modules\Payments\Contracts;

/** Result of a successful gateway session creation. */
final class SessionResult
{
    public function __construct(
        public readonly string $sessionId,
        public readonly string $providerOrderId,
        public readonly array $raw = [],
    ) {
    }
}
