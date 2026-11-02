<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditEvent;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Payment gateway callback endpoint.
 *
 * Payler sends a POST (x-www-form-urlencoded) with order_id whenever the
 * operation status changes. The callback carries no signature, so the
 * status is always re-verified with the gateway (GetStatus) before the
 * local payment is updated.
 */
class GatewayCallbackController extends Controller
{
    public function __invoke(Request $request, string $provider, PaymentService $payments): JsonResponse
    {
        $providerOrderId = (string) ($request->input('order_id')
            ?? $request->input('orderId')
            ?? $request->query('order_id', ''));

        $payment = $providerOrderId !== ''
            ? Payment::query()->where('provider_order_id', $providerOrderId)->first()
            : null;

        if (! $payment) {
            AuditEvent::record('payment.callback_unknown', $providerOrderId, 'system');

            // 200 stops gateway retry storms for unknown orders.
            return response()->json(['received' => true]);
        }

        try {
            $payments->syncStatus($payment);
        } catch (\Throwable $e) {
            report($e);

            // Let the gateway retry on transient failures.
            return response()->json(['error' => 'Status check failed'], 503);
        }

        return response()->json(['received' => true, 'status' => $payment->status]);
    }
}
