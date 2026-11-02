<?php

namespace App\Modules\Payments\Drivers;

use App\Modules\Payments\Contracts\PaymentGatewayException;
use App\Modules\Payments\Contracts\PaymentModule;
use App\Modules\Payments\Contracts\SessionRequest;
use App\Modules\Payments\Contracts\SessionResult;
use App\Modules\Payments\Contracts\StatusResult;
use App\Models\PaymentMerchant;
use Illuminate\Support\Facades\Http;

/**
 * Payler module (docs: https://docs.payler.com/ru-api).
 *
 * Gate scheme (standard web behaviour):
 *  1. POST {host}/gapi/StartSession (x-www-form-urlencoded) -> {session_id}
 *  2. Redirect the user to GET {host}/gapi/Pay?session_id=… (PayGate) —
 *     the hosted payment page. After the operation the user is returned
 *     to return_url_success / return_url_decline.
 *  3. Payler sends a guaranteed notification (callback) POST with order_id
 *     to the URL configured in the merchant back office; the status is
 *     always re-verified via POST {host}/gapi/GetStatus.
 */
class PaylerModule implements PaymentModule
{
    public function code(): string
    {
        return 'payler';
    }

    public function name(): string
    {
        return 'Payler';
    }

    public function startSession(PaymentMerchant $merchant, SessionRequest $request): SessionResult
    {
        $payload = array_filter([
            'key' => (string) $merchant->terminal_key,
            'type' => $merchant->operation_type ?: 'OneStep',
            'order_id' => $request->orderId,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'product' => $request->product,
            'lang' => in_array($request->lang, ['en', 'ru'], true) ? $request->lang : 'ru',
            'email' => $request->email,
            'lifetime' => max(1, $request->lifetimeMinutes),
            'return_url_success' => $request->returnUrlSuccess,
            'return_url_decline' => $request->returnUrlDecline,
        ], fn ($value) => $value !== null && $value !== '');

        $response = Http::asForm()
            ->timeout(20)
            ->retry(1, 300)
            ->post($this->gateUrl($merchant).'/gapi/StartSession', $payload);

        $data = $this->decode($response, 'StartSession');

        if (empty($data['session_id'])) {
            throw PaymentGatewayException::fromPayload('Payler', $data);
        }

        return new SessionResult(
            (string) $data['session_id'],
            (string) ($data['order_id'] ?? $request->orderId),
            $data,
        );
    }

    public function fetchStatus(PaymentMerchant $merchant, string $providerOrderId): StatusResult
    {
        $response = Http::asForm()
            ->timeout(20)
            ->retry(1, 300)
            ->post($this->gateUrl($merchant).'/gapi/GetStatus', [
                'key' => (string) $merchant->terminal_key,
                'order_id' => $providerOrderId,
            ]);

        try {
            $data = $this->decode($response, 'GetStatus');
        } catch (PaymentGatewayException $exception) {
            // 603 «Пользователь не предпринимал попыток оплаты» — the session
            // simply has no payment attempt yet: treat it as Created/pending.
            if ($exception->gatewayCode === 603) {
                return new StatusResult('pending', 'Created', 0, ['error' => $exception->getMessage()]);
            }

            throw $exception;
        }

        return new StatusResult(
            $this->normalizeStatus((string) ($data['status'] ?? '')),
            (string) ($data['status'] ?? ''),
            (int) ($data['amount'] ?? 0),
            $data,
        );
    }

    public function paymentPageUrl(PaymentMerchant $merchant, string $sessionId): string
    {
        // PayGate: the user is charged through the hosted payment page.
        // Docs: GET https://{host}.payler.com/gapi/Pay?session_id={session_id}
        return $this->gateUrl($merchant).'/gapi/Pay?'.http_build_query(['session_id' => $sessionId]);
    }

    /** URL of the callback endpoint this module exposes for a merchant. */
    public function callbackUrl(): string
    {
        return url('/api/payments/payler/callback');
    }

    private function gateUrl(PaymentMerchant $merchant): string
    {
        // Accept either "sandbox.payler.com" or "https://sandbox.payler.com".
        $host = trim((string) $merchant->host) ?: config('payments.payler.host');
        $host = preg_replace('#^https?://#i', '', $host);
        $host = rtrim((string) $host, '/');

        return 'https://'.$host;
    }

    private function decode(\Illuminate\Http\Client\Response $response, string $call): array
    {
        if ($response->serverError() || $response->clientError()) {
            $payload = $response->json();
            throw PaymentGatewayException::fromPayload(
                'Payler '.$call,
                is_array($payload) ? $payload : ['error' => ['message' => 'HTTP '.$response->status()]],
            );
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new PaymentGatewayException('Payler '.$call.': non-JSON response.');
        }

        if (isset($data['error'])) {
            throw PaymentGatewayException::fromPayload('Payler '.$call, $data);
        }

        return $data;
    }

    /** Map Payler operation statuses (см. «Статусы операций» в доках). */
    private function normalizeStatus(string $raw): string
    {
        return match ($raw) {
            'Charged' => 'paid',
            'Authorized' => 'authorized',
            'Refunded' => 'refunded',
            'Reversed' => 'reversed',
            'Rejected', 'Error' => 'failed',
            default => 'pending', // Created, Pending, PreAuthorized3DS…
        };
    }
}
