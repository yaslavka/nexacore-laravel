<?php

namespace App\Modules\Payments\Contracts;

use App\Models\PaymentMerchant;

/**
 * A payment system module. Each payment system (Payler, …) is a separate
 * module implementing this contract; the core application stays untouched
 * when a new module is added.
 */
interface PaymentModule
{
    /** Unique module code, e.g. "payler". */
    public function code(): string;

    /** Human readable module name. */
    public function name(): string;

    /** Create a payment session at the gateway. */
    public function startSession(PaymentMerchant $merchant, SessionRequest $request): SessionResult;

    /** Fetch the normalized operation status from the gateway. */
    public function fetchStatus(PaymentMerchant $merchant, string $providerOrderId): StatusResult;

    /** Hosted payment page URL the user should be redirected to. */
    public function paymentPageUrl(PaymentMerchant $merchant, string $sessionId): string;
}
