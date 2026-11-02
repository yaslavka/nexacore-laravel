<?php

namespace App\Modules\Payments\Contracts;

/**
 * Normalized gateway status.
 * One of: pending, authorized, paid, refunded, reversed, failed.
 */
final class StatusResult
{
    public function __construct(
        public readonly string $status,
        public readonly ?string $rawStatus = null,
        public readonly int $amount = 0,
        public readonly array $raw = [],
    ) {
    }
}
