<?php

namespace App\Services;

use App\Models\AuditEvent;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentMerchant;
use App\Models\PaymentProvider;
use App\Modules\Payments\Contracts\PaymentGatewayException;
use App\Modules\Payments\Contracts\PaymentModule;
use App\Modules\Payments\Contracts\SessionRequest;
use App\Modules\Payments\PaymentModuleManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Orchestrates the payment modules: merchant resolution by site theme,
 * session creation at the gateway, status synchronization and the
 * API charge flow (auto-created customer + auto-generated password).
 */
class PaymentService
{
    public function __construct(private PaymentModuleManager $modules)
    {
    }

    /* ------------------------------------------------------------------ */
    /*  Merchant resolution — merchants are bound to site themes           */
    /* ------------------------------------------------------------------ */

    /**
     * Find the active merchant of a payment system for a theme.
     * Falls back to the wildcard merchant ('*') of the same provider.
     */
    public function resolveMerchant(?string $providerCode, ?string $themeSlug): ?PaymentMerchant
    {
        $providers = PaymentProvider::query()->active()
            ->when($providerCode !== null && $providerCode !== '', fn ($query) => $query->where('code', $providerCode));

        $base = PaymentMerchant::query()->active()
            ->whereIn('payment_provider_id', $providers->select('id'));

        return (clone $base)
            ->where('theme_slug', $themeSlug !== null && $themeSlug !== '' ? $themeSlug : PaymentMerchant::ANY_THEME)
            ->first()
            ?? (clone $base)->where('theme_slug', PaymentMerchant::ANY_THEME)->first();
    }

    public function moduleFor(PaymentMerchant $merchant): PaymentModule
    {
        return $this->modules->forProvider($merchant->provider);
    }

    /* ------------------------------------------------------------------ */
    /*  Web checkout flow (standard behaviour)                             */
    /* ------------------------------------------------------------------ */

    /**
     * Start (or restart) the gateway session for a stored payment and
     * return the hosted payment page URL for the user to be redirected to.
     *
     * @return array{payment: Payment, paymentUrl: string}
     *
     * @throws PaymentGatewayException
     */
    public function startForPayment(Payment $payment): array
    {
        if ($payment->status === Payment::STATUS_PAID) {
            throw new PaymentGatewayException('Payment is already completed.');
        }

        if ($payment->isExpired()) {
            $payment->forceFill(['status' => 'expired'])->save();

            throw new PaymentGatewayException('Payment link has expired.');
        }

        $themeSlug = $payment->theme_slug ?: theme()->slug;
        $providerCode = $payment->provider?->code;

        $merchant = PaymentMerchant::find($payment->payment_merchant_id)
            ?? $this->resolveMerchant($providerCode, $themeSlug);

        if (! $merchant) {
            throw new PaymentGatewayException(
                'No active payment merchant is configured for theme ['.$themeSlug.'].'
            );
        }

        $module = $this->moduleFor($merchant);
        $session = $module->startSession($merchant, $this->sessionRequest($payment, $merchant, $themeSlug));
        $paymentUrl = $module->paymentPageUrl($merchant, $session->sessionId);

        $payment->payment_provider_id ??= $merchant->payment_provider_id;
        $payment->payment_merchant_id = $merchant->id;
        $payment->theme_slug = $payment->theme_slug ?: $themeSlug;
        $payment->session_id = $session->sessionId;
        $payment->provider_order_id = $session->providerOrderId;
        $payment->payment_url = $paymentUrl;
        $payment->mode = $merchant->isSandbox() ? 'sandbox' : 'live';
        $payment->session_requested = true;
        $payment->last_start_at = now();
        $payment->save();

        AuditEvent::record('payment.session_started', $payment->id, 'system');

        return ['payment' => $payment, 'paymentUrl' => $paymentUrl];
    }

    private function sessionRequest(Payment $payment, PaymentMerchant $merchant, string $themeSlug): SessionRequest
    {
        $order = $payment->order;
        $payload = $payment->payload ?? [];

        // Every gateway session needs a unique order id; retries get a suffix.
        $attempt = 1;
        if ($payment->provider_order_id) {
            $attempt = ((int) str_contains($payment->provider_order_id, '-')
                ? (int) Str::afterLast($payment->provider_order_id, '-')
                : 1) + 1;
        }
        $orderId = $attempt === 1 ? $payment->id : $payment->id.'-'.$attempt;

        $successUrl = $payload['return_url_success']
            ?? url('/checkout/'.$payment->id.'?result=success');
        $declineUrl = $payload['return_url_decline']
            ?? url('/checkout/'.$payment->id.'?result=decline');

        return new SessionRequest(
            orderId: $orderId,
            amount: (int) $payment->amount,
            currency: $payment->currency ?: $merchant->currency,
            product: mb_substr((string) ($payload['product'] ?? $order?->service_title ?? ''), 0, 250) ?: null,
            lang: in_array($payload['lang'] ?? null, ['en', 'ru'], true) ? $payload['lang'] : 'ru',
            email: $payment->customer?->email ?: $order?->email,
            customerId: $payment->customer?->id,
            returnUrlSuccess: $successUrl,
            returnUrlDecline: $declineUrl,
            lifetimeMinutes: (int) ($merchant->lifetime_minutes ?: config('payments.payler.lifetime_minutes', 60)),
            metadata: ['theme' => $themeSlug],
        );
    }

    /* ------------------------------------------------------------------ */
    /*  Status synchronization (callback + polling)                        */
    /* ------------------------------------------------------------------ */

    /**
     * Query the gateway for the fresh status and apply it locally.
     */
    public function syncStatus(Payment $payment): Payment
    {
        if (! $payment->provider_order_id || $payment->isTerminal()) {
            return $payment;
        }

        $merchant = $payment->merchant;
        if (! $merchant) {
            return $payment;
        }

        $status = $this->moduleFor($merchant)->fetchStatus($merchant, $payment->provider_order_id);

        $payment->provider_status = $status->rawStatus;
        $payment->payload = array_merge($payment->payload ?? [], ['last_status_raw' => $status->raw]);

        $this->applyStatus($payment, $status->status);

        return $payment->refresh();
    }

    /** Apply a normalized gateway status to the local payment + order. */
    public function applyStatus(Payment $payment, string $normalized): void
    {
        $map = [
            'paid' => 'paid',
            'authorized' => 'pending',
            'pending' => null, // keep current
            'refunded' => 'refunded',
            'reversed' => 'reversed',
            'failed' => 'cancelled',
        ];

        $local = $map[$normalized] ?? null;

        if ($local === null || $payment->status === $local) {
            $payment->save();

            return;
        }

        $payment->status = $local;

        if ($local === 'paid' && $payment->paid_at === null) {
            $payment->paid_at = now();
        }

        $payment->save();

        if ($payment->order) {
            $payment->order->payment_status = $local === 'paid' ? 'paid' : $local;
            $payment->order->save();
        }

        AuditEvent::record('payment.status.'.$normalized, $payment->id, 'system');
    }

    /* ------------------------------------------------------------------ */
    /*  API charge flow — creates the customer automatically               */
    /* ------------------------------------------------------------------ */

    /**
     * Create a customer (with an auto-generated password) and a payment,
     * open the gateway session and return everything the API consumer needs.
     *
     * @return array{
     *     payment: Payment, customer: Customer, customerCreated: bool,
     *     plainPassword: ?string, paymentUrl: string, merchant: PaymentMerchant
     * }
     *
     * @throws PaymentGatewayException
     */
    public function createCharge(array $input): array
    {
        $themeSlug = (string) ($input['theme'] ?? '') ?: theme()->slug;
        $providerCode = isset($input['provider']) ? (string) $input['provider'] : null;

        $merchant = $this->resolveMerchant($providerCode, $themeSlug);

        if (! $merchant) {
            throw new PaymentGatewayException(
                'No active payment merchant is configured for theme ['.$themeSlug.'].'
            );
        }

        [$customer, $customerCreated, $plainPassword] = $this->ensureCustomer($input);

        $payment = DB::transaction(function () use ($input, $customer, $merchant, $themeSlug) {
            $order = \App\Models\Order::query()->create([
                    'id' => (string) Str::uuid(),
                    'reference' => sprintf('API-%s', strtoupper(bin2hex(random_bytes(4)))),
                    'service_id' => 'api',
                    'service_title' => mb_substr((string) ($input['product'] ?? 'API payment'), 0, 200),
                    'package_name' => null,
                    'name' => $customer->name ?: $customer->email,
                    'email' => $customer->email,
                    'company' => null,
                    'message' => 'Created via payment API ('.$merchant->provider->name.', theme '.$themeSlug.')',
                    'consent' => true,
                    'consent_at' => now(),
                    'status' => 'new',
                    'payment_status' => 'pending',
                ]);

            $lifetime = max(1, (int) ($merchant->lifetime_minutes ?: 60));

            return Payment::query()->create([
                'id' => bin2hex(random_bytes(32)),
                'order_id' => $order?->id,
                'payment_provider_id' => $merchant->payment_provider_id,
                'payment_merchant_id' => $merchant->id,
                'customer_id' => $customer->id,
                'theme_slug' => $themeSlug,
                'amount' => (int) $input['amount'],
                'currency' => strtoupper((string) ($input['currency'] ?? $merchant->currency ?: config('payments.default_currency'))),
                'status' => 'created',
                'mode' => $merchant->isSandbox() ? 'sandbox' : 'live',
                'payload' => [
                    'product' => (string) ($input['product'] ?? ''),
                    'lang' => in_array($input['lang'] ?? null, ['en', 'ru'], true) ? $input['lang'] : 'ru',
                    'return_url_success' => $input['return_url_success'] ?? null,
                    'return_url_decline' => $input['return_url_decline'] ?? null,
                    'source' => 'api',
                    'metadata' => (object) ($input['metadata'] ?? []),
                ],
                'expires_at' => now()->addMinutes($lifetime),
            ]);
        });

        $module = $this->moduleFor($merchant);
        $session = $module->startSession($merchant, $this->sessionRequest($payment, $merchant, $themeSlug));
        $paymentUrl = $module->paymentPageUrl($merchant, $session->sessionId);

        $payment->forceFill([
            'session_id' => $session->sessionId,
            'provider_order_id' => $session->providerOrderId,
            'payment_url' => $paymentUrl,
            'session_requested' => true,
            'last_start_at' => now(),
        ])->save();

        AuditEvent::record('payment.api_charge', $payment->id, 'api');

        return [
            'payment' => $payment,
            'customer' => $customer,
            'customerCreated' => $customerCreated,
            'plainPassword' => $plainPassword,
            'paymentUrl' => $paymentUrl,
            'merchant' => $merchant,
        ];
    }

    /**
     * Find or create the customer and generate a password when created.
     * The password is emailed to the customer (log mailer by default) and
     * returned to the API caller for this single response.
     *
     * @return array{0: Customer, 1: bool, 2: ?string}
     */
    private function ensureCustomer(array $input): array
    {
        $email = strtolower(trim((string) $input['email']));

        $customer = Customer::query()->where('email', $email)->first();

        if ($customer) {
            $customer->fill(array_filter([
                'name' => isset($input['name']) ? trim((string) $input['name']) : '',
                'phone' => isset($input['phone']) ? trim((string) $input['phone']) : null,
            ], fn ($value) => $value !== null && $value !== ''));
            $customer->save();

            return [$customer, false, null];
        }

        $plainPassword = Str::password(14, symbols: false);

        $customer = Customer::query()->create([
            'email' => $email,
            'name' => trim((string) ($input['name'] ?? '')),
            'phone' => isset($input['phone']) ? trim((string) $input['phone']) : null,
            'password' => $plainPassword,
        ]);

        try {
            Mail::raw(
                "Здравствуйте!\n\n".
                "Для вас создан аккаунт на NexaCore.\n".
                "Логин: {$customer->email}\n".
                "Пароль: {$plainPassword}\n\n".
                'Рекомендуем сменить пароль после первого входа.',
                function ($message) use ($customer) {
                    $message->to($customer->email)->subject('NexaCore — доступ к аккаунту');
                }
            );
        } catch (\Throwable) {
            // Mail transport is best-effort; the password is still returned
            // in the API response and stored hashed in the database.
        }

        return [$customer, true, $plainPassword];
    }
}
