<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Optional shared-secret protection for the payment charge API.
 * Active only when PAYMENTS_API_TOKEN is configured.
 */
class VerifyPaymentsApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = (string) config('payments.api_token');

        if ($token === '') {
            return $next($request);
        }

        $provided = $request->header('X-Api-Key')
            ?? $request->bearerToken()
            ?? $request->query('api_token');

        if (! is_string($provided) || ! hash_equals($token, $provided)) {
            return response()->json(['error' => 'Invalid API token'], 401);
        }

        return $next($request);
    }
}
