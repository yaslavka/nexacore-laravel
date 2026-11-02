<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
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

    public function start(Request $request, string $token): JsonResponse
    {
        $payment = $this->findPayment($token);
        if (! $payment) {
            return response()->json(['error' => 'Payment link not found'], 404);
        }

        $data = json_decode((string) $request->getContent(), true);
        if (($data['consent'] ?? null) !== true) {
            return response()->json(['error' => 'Please accept the offer and refund policy'], 400);
        }

        // PSP (Payler) integration is planned next; until it is approved and configured
        // card payments stay visibly unavailable.
        return response()->json(['error' => 'Card payments are not configured'], 503);
    }

    public function status(string $token): JsonResponse
    {
        $payment = $this->findPayment($token);
        if (! $payment) {
            return response()->json(['error' => 'Payment link not found'], 404);
        }

        // Without a connected PSP the stored status is the source of truth.
        return response()->json(['payment' => $this->summary($payment->refresh())]);
    }
}
