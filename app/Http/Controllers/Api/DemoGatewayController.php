<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Completion endpoint of the built-in demo gateway page.
 * Simulates what the real PSP callback does: verifies the session and
 * marks the payment as paid.
 */
class DemoGatewayController extends Controller
{
    public function complete(Request $request, string $token): JsonResponse
    {
        if (! preg_match('/^[a-f0-9]{64}$/', $token)) {
            return response()->json(['error' => 'Invalid payment token'], 404);
        }

        $payment = Payment::query()->find($token);
        if (! $payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        if (! str_starts_with((string) $payment->session_id, \App\Modules\Payments\Drivers\DemoModule::PREFIX)) {
            return response()->json(['error' => 'Not a demo payment session'], 400);
        }

        if ($payment->status !== Payment::STATUS_PAID) {
            app(\App\Services\PaymentService::class)->applyStatus($payment, 'paid');
        }

        return response()->json(['status' => $payment->refresh()->status]);
    }
}
