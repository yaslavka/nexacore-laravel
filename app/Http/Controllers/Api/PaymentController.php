<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Modules\Payments\Contracts\PaymentGatewayException;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Public payment summary, identical to the original API contract.
     * Amount is in minor units (cents).
     */
    private function summary(Payment $payment): array
    {
        return [
            'reference' => $payment->order?->reference,
            'product' => (string) ($payment->payload['product'] ?? ''),
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'mode' => $payment->mode,
            'status' => $payment->status,
            'expiresAt' => $payment->expires_at?->toISOString(),
            'paidAt' => $payment->paid_at?->toISOString(),
            'expired' => $payment->expires_at !== null && $payment->expires_at->isPast(),
        ];
    }

    private function findPayment(string $token): ?Payment
    {
        if (! preg_match('/^[a-f0-9]{64}$/', $token)) {
            return null;
        }

        return Payment::query()->find($token);
    }

    public function show(string $token): JsonResponse
    {
        $payment = $this->findPayment($token);
        if (! $payment) {
            return response()->json(['error' => 'Payment link not found'], 404);
        }

        return response()->json(['payment' => $this->summary($payment)]);
    }

    /**
     * Start the payment: opens a session at the payment gateway (Payler
     * module or any other registered module) and returns the hosted
     * payment page URL. The frontend redirects the user to it.
     */
    public function start(Request $request, string $token, PaymentService $payments): JsonResponse
    {
        $payment = $this->findPayment($token);
        if (! $payment) {
            return response()->json(['error' => 'Payment link not found'], 404);
        }

        $data = json_decode((string) $request->getContent(), true);
        if (($data['consent'] ?? null) !== true) {
            return response()->json(['error' => 'Please accept the offer and refund policy'], 400);
        }

        if ($payment->isExpired() && $payment->status !== Payment::STATUS_PAID) {
            $payment->forceFill(['status' => 'expired'])->save();

            return response()->json(['error' => 'This payment link has expired'], 410);
        }

        try {
            $result = $payments->startForPayment($payment);
        } catch (PaymentGatewayException $e) {
            return response()->json(['error' => $e->getMessage()], 502);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['error' => 'Payment session could not be created'], 502);
        }

        return response()->json([
            'paymentUrl' => $result['paymentUrl'],
            'mode' => $result['payment']->mode,
        ]);
    }

    /**
     * Payment status used by the checkout page while polling. When a
     * gateway session exists and the payment is not final yet, the status
     * is refreshed from the gateway first.
     */
    public function status(string $token, PaymentService $payments): JsonResponse
    {
        $payment = $this->findPayment($token);
        if (! $payment) {
            return response()->json(['error' => 'Payment link not found'], 404);
        }

        try {
            if ($payment->provider_order_id && ! $payment->isTerminal()) {
                $payments->syncStatus($payment);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json(['payment' => $this->summary($payment->refresh())]);
    }
}
