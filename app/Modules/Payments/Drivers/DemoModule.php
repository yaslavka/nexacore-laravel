<?php

namespace App\Modules\Payments\Drivers;

use App\Modules\Payments\Contracts\PaymentModule;
use App\Modules\Payments\Contracts\SessionRequest;
use App\Modules\Payments\Contracts\SessionResult;
use App\Modules\Payments\Contracts\StatusResult;
use App\Models\PaymentMerchant;

/**
 * Built-in demo module. Simulates a hosted payment page so the whole
 * checkout flow can be verified end-to-end without real merchant
 * credentials. Never enable it in production.
 */
class DemoModule implements PaymentModule
{
    public const PREFIX = 'DEMO-';

    public function code(): string
    {
        return 'demo';
    }

    public function name(): string
    {
        return 'Demo gateway';
    }

    public function startSession(PaymentMerchant $merchant, SessionRequest $request): SessionResult
    {
        // The demo session id carries our payment token so the simulated
        // gateway page can find the payment again.
        $sessionId = self::PREFIX.$request->orderId;

        return new SessionResult($sessionId, $request->orderId, [
            'simulated' => true,
            'session_id' => $sessionId,
        ]);
    }

    public function fetchStatus(PaymentMerchant $merchant, string $providerOrderId): StatusResult
    {
        return new StatusResult('pending', 'Created', 0, ['simulated' => true]);
    }

    public function paymentPageUrl(PaymentMerchant $merchant, string $sessionId): string
    {
        $token = str_starts_with($sessionId, self::PREFIX)
            ? substr($sessionId, strlen(self::PREFIX))
            : $sessionId;

        return url('/pay/demo/'.$token);
    }
}
