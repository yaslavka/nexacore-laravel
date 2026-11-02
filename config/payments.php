<?php

/*
 * Payment module configuration.
 *
 * Every payment system (Payler, future ones) is a separate module class
 * implementing App\Modules\Payments\Contracts\PaymentModule. Modules are
 * registered here and can be enabled per payment provider record in the
 * admin panel.
 */

return [

    /*
     * Shared API token. When set (PAYMENTS_API_TOKEN), every charge API
     * request must carry it in the X-Api-Key header.
     */
    'api_token' => env('PAYMENTS_API_TOKEN'),

    /*
     * Default currency for API charges when the request omits it.
     */
    'default_currency' => env('PAYMENTS_DEFAULT_CURRENCY', 'KGS'),

    /*
     * Payment module registry: code => module class.
     */
    'drivers' => [
        'payler' => \App\Modules\Payments\Drivers\PaylerModule::class,
        'demo' => \App\Modules\Payments\Drivers\DemoModule::class,
    ],

    /*
     * Payler defaults (docs: https://docs.payler.com/ru-api).
     * The Gate scheme: StartSession -> gpay/{session_id} -> callback
     * (order_id) -> GetStatus.
     */
    'payler' => [
        'host' => env('PAYLER_HOST', 'secure.payler.com'),
        'sandbox_host' => env('PAYLER_SANDBOX_HOST', 'sandbox.payler.com'),
        'lifetime_minutes' => (int) env('PAYLER_LIFETIME_MINUTES', 60),
    ],

];
