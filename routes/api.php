<?php

use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\ChargeController;
use App\Http\Controllers\Api\DemoGatewayController;
use App\Http\Controllers\Api\GatewayCallbackController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json(['status' => 'ok']));

Route::get('/settings', SettingsController::class);
Route::get('/catalog', CatalogController::class);

Route::post('/orders', [OrderController::class, 'store'])
    ->middleware('throttle:20,60');

Route::get('/payments/{token}', [PaymentController::class, 'show'])
    ->where('token', '[a-f0-9]{64}')
    ->middleware('throttle:90,1');
Route::post('/payments/{token}/start', [PaymentController::class, 'start'])
    ->where('token', '[a-f0-9]{64}')
    ->middleware('throttle:90,1');
Route::post('/payments/{token}/status', [PaymentController::class, 'status'])
    ->where('token', '[a-f0-9]{64}')
    ->middleware('throttle:90,1');

/*
 * Payment modules (each payment system is a module):
 *  - charge: API that creates the customer (auto password) and returns the
 *    hosted payment form URL for the merchant bound to the requested theme;
 *  - callback: gateway notifications (no signature — status is re-verified
 *    with the gateway before applying).
 */
Route::post('/v1/payments/{provider}/charge', [ChargeController::class, 'store'])
    ->where('provider', '[a-z0-9_-]+')
    ->middleware(['throttle:30,1', App\Http\Middleware\VerifyPaymentsApiToken::class]);

Route::post('/payments/{provider}/callback', GatewayCallbackController::class)
    ->where('provider', '[a-z0-9_-]+')
    ->middleware('throttle:120,1');

// Completion endpoint of the simulated demo gateway page (API variant).
Route::post('/payments/{token}/demo/complete', [DemoGatewayController::class, 'complete'])
    ->where('token', '[a-f0-9]{64}')
    ->middleware('throttle:30,1');
