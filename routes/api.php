<?php

use App\Http\Controllers\Api\CatalogController;
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
