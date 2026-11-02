<?php

namespace App\Modules\Payments\Contracts;

use RuntimeException;

/** Raised when a payment gateway rejects a request or answers invalid data. */
class PaymentGatewayException extends RuntimeException
{
    public ?int $gatewayCode = null;

    public static function fromPayload(string $module, array $payload): self
    {
        $exception = new self(sprintf(
            '%s: %s',
            $module,
            (string) ($payload['error']['message'] ?? 'Unexpected gateway response'),
        ));

        $exception->gatewayCode = isset($payload['error']['code']) ? (int) $payload['error']['code'] : null;

        return $exception;
    }
}
