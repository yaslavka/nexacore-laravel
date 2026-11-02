<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentProvider;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Public list of payment systems available on the site.
 *
 * GET /api/payments/methods[?theme={slug}]
 *  - returns active payment providers that have an active merchant for the
 *    requested theme (wildcard merchants included);
 *  - the public site uses it to render the "choose a payment system" modal
 *    before the client charge request.
 */
class PaymentMethodsController extends Controller
{
    public function __invoke(Request $request, PaymentService $payments): JsonResponse
    {
        $themeSlug = (string) $request->query('theme', '') ?: theme()->slug;

        $methods = PaymentProvider::query()
            ->active()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (PaymentProvider $provider): array => [
                'provider' => $provider,
                'merchant' => $payments->resolveMerchant($provider->code, $themeSlug),
            ])
            ->filter(fn (array $row): bool => $row['merchant'] !== null)
            ->values()
            ->map(fn (array $row): array => [
                'code' => $row['provider']->code,
                'name' => $row['provider']->name,
                'mode' => $row['merchant']->isSandbox() ? 'sandbox' : 'live',
                'currency' => (string) ($row['merchant']->currency ?: config('payments.default_currency', 'KGS')),
            ]);

        return response()->json([
            'theme' => $themeSlug,
            'methods' => $methods,
        ]);
    }
}
