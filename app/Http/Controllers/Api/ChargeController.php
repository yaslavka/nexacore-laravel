<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Payment charge API.
 *
 * POST /api/v1/payments/{provider}/charge
 *  - creates the customer in the database (auto-generated password, sent
 *    to the customer and returned once in this response);
 *  - opens a gateway session for the merchant bound to the requested theme;
 *  - returns the URL of the hosted payment form with all required data
 *    already attached (session id).
 */
class ChargeController extends Controller
{
    public function store(Request $request, string $provider, PaymentService $payments): JsonResponse
    {
        $data = $request->json()->all();
        if (! is_array($data) || array_is_list($data)) {
            return response()->json(['error' => 'Invalid JSON body'], 400);
        }

        // An API endpoint always answers JSON, even when the client omits
        // the Accept header (the default validation flow would redirect).
        $validator = Validator::make($data, [
            'email' => ['required', 'email:rfc', 'max:254'],
            'name' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'amount' => ['required', 'integer', 'min:1'],
            'currency' => ['nullable', 'string', 'size:3', 'alpha'],
            'product' => ['nullable', 'string', 'max:250'],
            'theme' => ['nullable', 'string', 'max:60', 'regex:/^[a-z0-9][a-z0-9_-]*\*?$/i'],
            'lang' => ['nullable', Rule::in(['en', 'ru'])],
            'return_url_success' => ['nullable', 'url', 'max:1000'],
            'return_url_decline' => ['nullable', 'url', 'max:1000'],
            'metadata' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'errors' => $validator->errors()->toArray(),
            ], 422);
        }

        $validated = $validator->validated();

        $input = $validated + ['provider' => $provider];

        try {
            $charge = $payments->createCharge($input);
        } catch (\App\Modules\Payments\Contracts\PaymentGatewayException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['error' => 'Payment session could not be created'], 502);
        }

        $payment = $charge['payment'];

        return response()->json([
            'customer' => [
                'id' => $charge['customer']->id,
                'email' => $charge['customer']->email,
                'name' => $charge['customer']->name,
                'created' => $charge['customerCreated'],
                // Auto-generated password is delivered once: here + by email.
                'password' => $charge['plainPassword'],
            ],
            'payment' => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'status' => $payment->status,
                'expiresAt' => $payment->expires_at?->toISOString(),
            ],
            'merchant' => [
                'name' => $charge['merchant']->name,
                'theme' => $payment->theme_slug,
                'provider' => $provider,
            ],
            'paymentUrl' => $charge['paymentUrl'],
        ], 201);
    }
}
